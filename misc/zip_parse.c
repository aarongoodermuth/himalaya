/**
 * @file zip_parse.c
 * @brief preprocessor for ZIP code listing, adds records to 
 *   ZIP_Codes table in himalaya MySQL database
 * @date 2/23/13
 * @author Brian Golden
 *
 */

#define _BSD_SOURCE
#define _SVID_SOURCE
#include <stdio.h>   /* snprintf()   */
#include <stdlib.h>
#include <unistd.h>  /* getpass()    */
#include <string.h>  /* strerror()   */
#include <ctype.h>   /* isspace()    */
#include <errno.h>
#include <mysql.h>

#define PSIZE    128
#define SERVER   "localhost"
#define DATABASE "himalaya"
#define BUFLEN   512

static const char *prog;  /*  program name */

/**
 * add_row - add a new record to the ZIP_Codes table in the database
 * @param conn: existing handle to the database
 * @param zip: ZIP code for new record
 * @param city: corresponding city for zip
 * @param state: corresponding stat for zip
 * @param lat: corresponding latitude for zip
 * @param lon: corresponding longitude for zip
 */
static int add_row(MYSQL *conn, const char *zip, const char *city, 
		   const char *state, const char *lat, const char *lon)
{
	char query[512];

	snprintf(query, 512, "INSERT INTO ZIP_Codes (zip, city, zstate, lat, " 
			     "lon) VALUES ('%s', '%s', '%s', %s, %s)", 
			     zip, city, state, lat, lon);

	printf("%s\n", query);

	if (mysql_query(conn, query)) {
		fprintf(stderr, "%s: error with query `%s': %s\n", 
				prog, query, mysql_error(conn));
		return -1;
	}

	return 0;
}

/**
 * parse_line - parses a line from the ZIP code file, extracts each part
 * @param line: a single line of the ZIP code file
 * @param zip: ZIP code from line
 * @param city: corresponding city for zip
 * @param state: corresponding stat for zip
 * @param lat: corresponding latitude for zip
 * @param lon: corresponding longitude for zip
 * @return 0 success, -1 failure
 */
int parse_line(char *line, char *zip, char *city, 
	       char *state, char *lat, char *lon)
{
	char *tok;
	char temp[256];
	char *end;
	char *p;
	size_t len;
	int i;

	i = 0;

	tok = NULL;
	tok = strtok(line, ",");
	while(tok != NULL) {
		strncpy(temp, tok + 1, 256);

		len = strlen(temp);
		end = temp + len - 1;
		while(end >= temp && *end == ' ')
			end--;
		*(end + 1) = '\0';

		if ((p = strrchr(temp, '\"')) != NULL)
			*p = '\0';

		switch(i) {
		case 0:  /* zip code */
			strncpy(zip, temp, 6);
			break;
		case 1:  /* city */
			strncpy(city, temp, 256);
			break;
		case 2:  /* state */
			strncpy(state, temp, 3);
			break;
		case 3:  /* latitude */
			strncpy(lat, temp, 16);
			break;
		case 4:  /* longitude */
			strncpy(lon, temp, 16);
			break;
		default:
			break;
		}

		i++;
		tok = strtok(NULL, ",");
	}

	return 0;
}


/**
 * parse_zips - parse the input file of ZIP codes and corresponding 
 *   city, state, latitude and longitude values
 * @param pathname: path to input file
 * @param conn: existing handle to the database
 * @return 0 success, -1 failure
 */
int parse_zips(const char *pathname, MYSQL *conn) 
{
	FILE *f;
	char buf[BUFLEN];
	char zip[6];
	char city[256];
	char state[3];
	char lat[16];
	char lon[16];
	char *p, *end;
	size_t len;
	int ret;

	ret = 0;

	if ((f = fopen(pathname, "r")) == NULL) {
		fprintf(stderr, "%s: failed to open %s for reading: %s\n", 
				prog, pathname, strerror(errno));
		return -1;
	}

	while((p = fgets(buf, sizeof(buf), f)) != NULL) {
		if (buf[strlen(buf) - 1] == '\n')
			buf[strlen(buf) - 1] = '\0';

		while (p && *p && isspace(*p))
			++p;

		if (!p || !(*p))
			continue;

		/* remove trailing whitespace */
		len = strlen(p);
		end = p + len - 1;
		while(end >= p && *end == ' ')
			end--;
		*(end + 1) = '\0';

		if (parse_line(buf, zip, city, state, lat, lon) == -1) {
			fprintf(stderr, "%s: parse_line() failed on `%s'\n", prog, buf);
			ret = -1;
		} else if (add_row(conn, zip, city, state, lat, lon) == -1) {
			fprintf(stderr, "%s: add_row() failed\n", prog);
			ret = -1;
		}
	}

	if (fclose(f) == -1) {	
		fprintf(stderr, "%s: fclose() error: %s\n", prog, strerror(errno));
		return -1;
	}

	return ret;
}

int main(int argc, char **argv)
{
	const char *user;      /* db username */
	const char *password;  /* user's db password */ 
	const char *server;    /* connection server */
	const char *database;  /* name of db to connect to  */
	char prompt[PSIZE];    /* password prompt */
	MYSQL *conn;           /* handle to database */
	int ret;               /* program return value */

	prog = argv[0];
	server = SERVER;
	database = DATABASE;
	ret = EXIT_SUCCESS;

	if (argc < 3) {
		fprintf(stderr, "%s: provide a username and input file, " 
				"e.g. `%s username input.txt'\n", prog, prog);
		return EXIT_FAILURE;
	}

	user = argv[1];

	snprintf(prompt, PSIZE, "enter database password for %s: ", user);
	
	if ((password = getpass(prompt)) == NULL) {  /* failed to get password */
		fprintf(stderr, "%s: you must enter a password\n", prog); 
		return EXIT_FAILURE;
	}


	conn = mysql_init(NULL);

	/* connect to the database */
	if (mysql_real_connect(conn, server, user, 
			       password, database, 0, NULL, 0) == NULL) {
		fprintf(stderr, "%s: mysql_real_connect() failed: %s\n", 
				prog, mysql_error(conn));
		return EXIT_FAILURE;
	}

	if (parse_zips(argv[2], conn) == -1) {
		fprintf(stderr, "%s: there were errors adding ZIP codes "
				"to the database\n", prog);
		ret = EXIT_FAILURE;
	}


	mysql_close(conn);

	return ret;
}
