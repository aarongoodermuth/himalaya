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

#define SERVER   "localhost"
#define DATABASE "himalaya"

static const char *prog;  /*  program name */

/**
 * add_row - add a new record to the ZIP_Codes table in the database
 * @param conn: existing handle to the database
 * @param date: date string formatted as 'YYYY-MM-DD HH:MM:SS'
 * @return 0 success, -1 failure
 */
static int get_finished_auctions_since(MYSQL *conn, const char *date)
{
	char query[512];

	snprintf(query, 
		 512, 
		 "SELECT * "
		 "FROM Auctions " 
		 "WHERE end_date > ", 
			     date);

	printf("%s\n", query);

	if (mysql_query(conn, query)) {
		fprintf(stderr, "%s: error with query `%s': %s\n", 
				prog, query, mysql_error(conn));
		return -1;
	}

	return 0;
}

int main(int argc, char **argv)
{
	const char *server;    /* connection server */
	const char *database;  /* name of db to connect to  */
	const char *datstr;
	MYSQL *conn;           /* handle to database */
	int ret;               /* program return value */

	prog = argv[0];
	server = SERVER;
	database = DATABASE;
	ret = EXIT_SUCCESS;

	if (argc < 2) {
		fprintf(stderr, "%s: provide a date to check from, " 
				"e.g. `%s `date +'%F %T'`'\n", prog, prog);
		return EXIT_FAILURE;
	}

	datestr = argv[1];
	conn = mysql_init(NULL);

	/* connect to the database */
	if (mysql_real_connect(conn, server, "root", "431w", 
			       database, 0, NULL, 0) == NULL) {
		fprintf(stderr, "%s: mysql_real_connect() failed: %s\n", 
				prog, mysql_error(conn));
		return EXIT_FAILURE;
	}

	if (get_finished_auctions_since(datestr, conn) == -1) 
		ret = EXIT_FAILURE;

	mysql_close(conn);

	return ret;
}
