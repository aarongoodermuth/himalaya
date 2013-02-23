PNAME=zipparse
SRC=zip_parse.c

${PNAME}: ${SRC}
	gcc -Wall -Wextra -Werror -std=c89 -o ${PNAME} $(shell mysql_config --cflags) ${SRC} $(shell mysql_config --libs)

clean: 
	rm -f *.o a.out ${PNAME}
