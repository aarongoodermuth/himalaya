#!/bin/bash

set -e 

DBNAME="himalaya"
DBUSER="root"
DBPASS="431w"
depth=1
count=0
PARENTS=/tmp/parents

# get children of a category
# usage: children depth categoryid
function children()
{
	FILENAME=/tmp/children_$depth
	rm -f $FILENAME

	mysql -u $DBUSER -p${DBPASS} -e \
                "SELECT C.category_id, C.category_name 
                 FROM   Categories C 
                 WHERE  C.parent_category_id = $2 
                 INTO   OUTFILE '$FILENAME' FIELDS TERMINATED BY ',' 
                        ENCLOSED BY '\"' LINES TERMINATED BY '\\n'" $DBNAME

	cat $FILENAME | while read LINE
	do	
		ID=`echo "$LINE" | cut -d',' -f1 | sed "s/\"//g"`
		CAT=`echo "$LINE" | cut -d',' -f2 | sed -e "s/\"//g" -e "s/\&/\&amp;/g"`
		printf "<option value=\"$ID\">"
		for i in $(seq $1 $END)
		do
			printf "&nbsp;&nbsp;"
		done
		echo "|-- $CAT</option>"
		children $(($1+1)) $ID
	done
}

# get all categories with root as parent
# for all those categories
	# go depth first down the tree, keep track of depth
	# for each, print with tabs(?)
rm -f $PARENTS
mysql -u $DBUSER -p${DBPASS} -e \
        "SELECT C.category_id, C.category_name 
         FROM   Categories C 
         WHERE  C.parent_category_id = 0 
         INTO   OUTFILE '$PARENTS' FIELDS TERMINATED BY ',' 
                ENCLOSED BY '\"' LINES TERMINATED BY '\\n'" $DBNAME

echo -e "
<select value=\"category\" class=\"span4\" name=\"category\">
<option>&nbsp;</option>"

cat $PARENTS | while read LINE
do
	count=$((count+1))
	[ $count -eq 1 ] && continue

	ID=`echo "$LINE" | cut -d',' -f1 | sed "s/\"//g"`
	CAT=`echo "$LINE" | cut -d',' -f2 | sed -e "s/\"//g" -e "s/\&/\&amp;/g"`
	echo -e "<option value=\"$ID\">$CAT</option>"
	children $depth $ID 
done

echo "</select>"

exit 0
