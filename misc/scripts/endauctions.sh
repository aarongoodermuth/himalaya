#!/bin/bash

set -e 

DBNAME="himalaya"
DBUSER="root"
DBPASS="431w"
AUCTIONS=/tmp/finished_acutions

# terminate an auction by item_id
# usage: terminate item_id
function terminate()
{
	LOSERSFILE=/tmp/auction_$1_losers
	WINFILE=/tmp/auction_$1_winner
	SELLERFILE=/tmp/auction_$1_seller
	ITEMFILE=/tmp/auction_$1_item

	rm -f $LOSERSFILE $WINFILE $SELLERFILE $ITEMFILE

	# get emails of all non-winning bidders
	mysql -u $DBUSER -p${DBPASS} -e \
		"SELECT E.email
	         FROM   Emails E, Has_Bid H, Auctions A
		 WHERE  H.item_id=$1 AND H.item_id=A.item_id AND 
		        E.username=H.bidder AND H.bidder <> A.recent_bidder
		 INTO   OUTFILE '$LOSERSFILE' 
		        LINES TERMINATED BY '\\n'" $DBNAME

	# get reserve price, last bid, winner's email, 
	#   first address, first credit card
	mysql -u $DBUSER -p${DBPASS} -e \
		"SELECT A.recent_bidder, A.recent_bid, A.reserve_price,
	                E.email, D.street, Z.city, Z.zstate, D.zip, 
			C.cnumber, C.ctype, C.cname
		 FROM   Auctions A, Emails E, Addresses D, 
		        Has_Card H, ZIP_Codes Z, Credit_Cards C
		 WHERE  A.item_id=$1 AND E.username=A.recent_bidder AND
		        A.recent_bidder=D.username AND Z.zip=D.zip AND
			A.recent_bidder=H.username AND H.cnumber=C.cnumber
		 LIMIT  1
		 INTO   OUTFILE '$WINFILE' FIELDS TERMINATED BY ','
		        LINES TERMINATED BY '\\n'" $DBNAME

	# get email of seller
	mysql -u $DBUSER -p${DBPASS} -e \
		"SELECT S.username, E.email
		 FROM   Emails E, Sale_Items S
		 WHERE  S.item_id=$1 AND E.username=S.username
		 INTO   OUTFILE '$SELLERFILE' FIELDS TERMINATED BY ','
		        LINES TERMINATED BY '\\n'" $DBNAME
	
	# get item information
	mysql -u $DBUSER -p${DBPASS} -e \
		"SELECT P.p_name, S.scondition, Z.city, Z.zstate, S.shipping_zip
	         FROM   Products P, Sale_Items S, ZIP_Codes Z
		 WHERE  item_id=$1 AND S.product_id=P.product_id AND 
		        S.shipping_zip=Z.zip
		 INTO   OUTFILE '$ITEMFILE' FIELDS TERMINATED BY ','
		        LINES TERMINATED BY '\\n'" $DBNAME

	WINNER=`cat $WINFILE | cut -d',' -f1`
	LASTBID=`cat $WINFILE | cut -d',' -f2`
	RESERVE=`cat $WINFILE | cut -d',' -f3`
	WINEMAIL=`cat $WINFILE | cut -d',' -f4`
	TOSTREET=`cat $WINFILE | cut -d',' -f5`
	TOCITY=`cat $WINFILE | cut -d',' -f6`
	TOSTATE=`cat $WINFILE | cut -d',' -f7`
	TOZIP=`cat $WINFILE | cut -d',' -f8`
	CARDNUM=`cat $WINFILE | cut -d',' -f9`
	CARDTYPE=`cat $WINFILE | cut -d',' -f10`
	CARDNAME=`cat $WINFILE | cut -d',' -f11`
	SELLER=`cat $SELLERFILE | cut -d',' -f1`
	SELLEMAIL=`cat $SELLERFILE | cut -d',' -f2`
	PNAME=`cat $ITEMFILE | cut -d',' -f1`
	CONDITION=`cat $ITEMFILE | cut -d',' -f2`
	FROMCITY=`cat $ITEMFILE | cut -d',' -f3`
	FROMSTATE=`cat $ITEMFILE | cut -d',' -f4`
	FROMZIP=`cat $ITEMFILE | cut -d',' -f5`

	# get price as 2-digit dollar value
	RSCALED=`echo "scale=2; $RESERVE / 100" | bc`
	ADJPRICE=`printf "%.2f" $RSCALED`

	# if the reserve price was not met
	if [ $RESERVE -gt $LASTBID ]
	then
		echo auction failed, item_id = $1
		echo reserve price $ADJPRICE is greater than last bid $LASTBID

		#mysql -u $DBUSER -p${DBPASS} -e \
		#	"DELETE FROM Auctions WHERE item_id=$1; 
		#         DELETE FROM Sale_Items WHERE item_id=$1" $DBNAME
		# notify seller and highest bidder

	else
		echo auction succeeded, item_id = $1
		echo reserve price $ADJPRICE is less than or equal to last bid $LASTBID
		# create an orders record
		#mysql -u $DBUSER -p${DBPASS} -e \
		#	"INSERT INTO Orders 
		#        VALUES ($1, $WINNER, 0, $TODAY, $LASTBID, 
		#	         $WINSTREET, $WINZIP, $WINCNUM)" $DBNAME

		# notify buyer and seller that the auction ended
		# sendmessage email messagefile subjectline
	fi

	cat $LOSERSFILE | while read EMAIL
	do
		echo sending email to $EMAIL, subject
	done

	echo
}

#####################

rm -f $AUCTIONS

TIME=`date --date "2 weeks ago" +"%F %T"`
TODAY=`date +%F`

mysql -u $DBUSER -p${DBPASS} -e \
	"SELECT A.item_id 
	 FROM   Auctions A 
	 WHERE  A.end_date < \"$TIME\" 
	 INTO   OUTFILE '$AUCTIONS' LINES TERMINATED BY '\\n'" $DBNAME

cat $AUCTIONS | while read LINE
do
	terminate $LINE
done

exit 0
