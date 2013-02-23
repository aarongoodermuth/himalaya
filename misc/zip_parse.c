#define _BSD_SOURCE
#define _SVID_SOURCE
#include <stdio.h>   /* snprintf()   */
#include <stdlib.h>
#include <unistd.h>  /* getpass()    */
#include <string.h>  /* strerror()   */
#include <errno.h>
#include <mysql.h>

#define PSIZE    128
#define SERVER   "localhost"
#define DATABASE "himalaya"

static const char *prog;  /*  program name */

/**
 * parse_zips - parse the input file of ZIP codes and corresponding 
 *   city, state, latitude and longitude values
 * @param pathname: path to input file
 * @return 0 success, -1 failure
 */
int parse_zips(const char *pathname) 
{
	FILE *fp;
	char buf[512];
	/* char *p;*/

	if ((fp = fopen(pathname, "r")) == NULL) {
		fprintf(stderr, "%s: fopen() error: %s\n", prog, strerror(errno));
		return -1;
	}

	while((f




	if (fclose(fp) == -1) {	
		fprintf(stderr, "%s: fclose() error: %s\n", prog, strerror(errno));
		return -1;
	}

	return 0;
}

int main(int argc, char **argv)
{
	const char *user;      /* db username */
	const char *password;  /* user's db password */ 
	const char *server;    /* connection server */
	const char *database;  /* name of db to connect to  */
	char prompt[PSIZE];    /* password prompt */
	MYSQL *conn;           /* handle to database */
	MYSQL_RES *res;        /* queury result */
	MYSQL_ROW row;         /*  one row of result */

	prog = argv[0];
	server = SERVER;
	database = DATABASE;

	if (argc < 2) {
		fprintf(stderr, "%s: provide a username as argument, e.g. `%s username'\n", prog, prog); 
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
	if (mysql_real_connect(conn, server, user, password, database, 0, NULL, 0) == NULL) {
		fprintf(stderr, "%s: mysql_real_connect() failed: %s\n", prog, mysql_error(conn));
		return EXIT_FAILURE;
	} else if (mysql_query(conn, "show tables")) {
		fprintf(stderr, "%s: mysql_query() error: %s\n", prog, mysql_error(conn));
	}

	res = mysql_use_result(conn);
	printf("MySQL tables in mysql database:\n");

	while ((row = mysql_fetch_row(res)) != NULL)
		printf("%s\n", row[0]);

	mysql_free_result(res);
	mysql_close(conn);

	return EXIT_SUCCESS;
}
