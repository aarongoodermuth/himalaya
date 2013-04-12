#!/bin/bash

set -e 

DBNAME="himalaya"
DBUSER="root"
DBPASS="431w"
depth=1
count=0
PARENTS=/tmp/xml_parents

# get children of a category
# usage: children depth categoryid
function children()
{
	FILENAME=/tmp/xml_children_$depth
	rm -f $FILENAME

	mysql -u $DBUSER -p${DBPASS} -e "SELECT C.category_id, C.category_name, C.parent_category_id FROM Categories C WHERE  C.parent_category_id = $2 INTO OUTFILE '$FILENAME' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\n'" $DBNAME

	cat $FILENAME | while read LINE
	do	
		ID=`echo "$LINE" | cut -d',' -f1 | sed "s/\"//g"`
		CAT=`echo "$LINE" | cut -d',' -f2 | sed -e "s/\"//g" -e "s/\&/\&amp;/g"`
		PID=`echo "$LINE" | cut -d',' -f3 | sed "s/\"//g"`
		printf "<category>\n  <id>$ID</id>\n  <name>$CAT</name>\n  <pid>$PID</pid>\n  <depth>$(($1+1))</depth>\n</category>\n"
		children $(($1+1)) $ID
	done
}

# get all categories with root as parent
# for all those categories
	# go depth first down the tree, keep track of depth
rm -f $PARENTS
mysql -u $DBUSER -p${DBPASS} -e "SELECT C.category_id, C.category_name, C.parent_category_id FROM Categories C WHERE C.parent_category_id = 0 INTO OUTFILE '$PARENTS' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\n'" $DBNAME

echo -e "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
echo "<category_tree>"
printf "<category>\n  <id>0</id>\n  <name>All Items</name>\n  <pid>0</pid>\n  <depth>0</depth>\n</category>\n"

cat $PARENTS | while read LINE
do
	count=$((count+1))
	[ $count -eq 1 ] && continue

	ID=`echo "$LINE" | cut -d',' -f1 | sed "s/\"//g"`
	CAT=`echo "$LINE" | cut -d',' -f2 | sed -e "s/\"//g" -e "s/\&/\&amp;/g"`
	PID=`echo "$LINE" | cut -d',' -f3 | sed "s/\"//g"`
	printf "<category>\n  <id>$ID</id>\n  <name>$CAT</name>\n  <pid>$PID</pid>\n  <depth>$depth</depth>\n</category>\n"
	children $depth $ID 
done

echo "</category_tree>"

exit 0
