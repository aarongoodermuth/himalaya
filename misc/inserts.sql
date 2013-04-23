/* ***** Categories ***** */
/* (category_id, category_name, parent_category_id) */
INSERT INTO Categories VALUES (0, "All Items", 0);
INSERT INTO Categories VALUES (1, "Home & Office", 0);
INSERT INTO Categories VALUES (2, "Sports & Outdoors", 0);
INSERT INTO Categories VALUES (3, "Electronics", 0);
INSERT INTO Categories VALUES (4, "Clothing", 0);
INSERT INTO Categories VALUES (5, "Books", 0);
INSERT INTO Categories VALUES (22, "Pet Care", 0);
INSERT INTO Categories VALUES (23, "Food & Drink", 0);

INSERT INTO Categories VALUES (6, "Home Improvement", 1);
INSERT INTO Categories VALUES (7, "Lawn & Garden", 1);

INSERT INTO Categories VALUES (8, "Sports Equipment", 2);
INSERT INTO Categories VALUES (9, "Camping Gear", 2);

INSERT INTO Categories VALUES (10, "Computers", 3);
INSERT INTO Categories VALUES (11, "MP3 Players", 3);
INSERT INTO Categories VALUES (12, "Tablets", 3);
INSERT INTO Categories VALUES (20, "Video Games", 3);
INSERT INTO Categories VALUES (21, "Appliances", 3);

INSERT INTO Categories VALUES (13, "Shoes", 4);
INSERT INTO Categories VALUES (14, "Formal Wear", 4);

INSERT INTO Categories VALUES (15, "Laptops", 10);
INSERT INTO Categories VALUES (16, "Desktops", 10);

INSERT INTO Categories VALUES (17, "Golf", 8);
INSERT INTO Categories VALUES (18, "Curling", 8);

INSERT INTO Categories VALUES (19, "Curling Stones", 18);

INSERT INTO Categories VALUES (24, "Beverages", 23);
INSERT INTO Categories VALUES (25, "Food", 23);

INSERT INTO Categories VALUES (26, "Alcoholic Beverages", 24);


/* ***** Suppliers ***** */
INSERT INTO Members VALUES('homedepot', '$2a$08$PPawxHEng3IwfeJW6g1psumXGZt6hzALVJX8gaZYmyHzqRfdk7a1a'
                           /*'supplier'*/, NULL);
INSERT INTO Suppliers VALUES('homedepot', 'Home Depot', 'John Smith');
INSERT INTO Emails VALUES('homedepot', 'sales@homedepot.com');
INSERT INTO Phones VALUES('homedepot', '18004663337');
INSERT INTO Addresses VALUES('2615 Green Tech Drive', '16803', 'Home Depot', 'homedepot');
INSERT INTO Sells_In VALUES('homedepot', 6);


/* ***** Registered Users ***** */
/* (username, name, gender, dob, income, gift_card_balance) */
INSERT INTO Members VALUES('boyscout', '$2a$08$n38sv7SVomptthTtbnMLQ.NLehwkf64pa6qRCYl2DvOvxGV7hPk8i'
                           /*'normalform'*/, NULL);
INSERT INTO Registered_Users VALUES('boyscout', 'Boyce-Cott', 'M', '1980-07-08', 1000000, 0);
INSERT INTO Emails VALUES('boyscout', 'betterthan@3nf.org');
INSERT INTO Phones VALUES('boyscout', '3141592645');
INSERT INTO Credit_Cards VALUES('1234567890123456', 'Visa', 'Boyce-Cott', '2014-12-31');
INSERT INTO Has_Card VALUES('1234567890123456', 'boyscout');
INSERT INTO Addresses VALUES('78 N Park Ave', '16802', 'Boyce-Cott', 'boyscout');

INSERT INTO Members VALUES('briang', '$2a$08$e3uCknP/pgZgST6cqKN/4.X0ElrzoponLS5qHsR4ff73FTQ3KgdIi'
                           /*'golden'*/, NULL);
INSERT INTO Registered_Users VALUES('briang', 'Brian Golden', 'M', '1992-05-09', 1000000, 2000);
INSERT INTO Emails VALUES('briang', 'brian.golden09@gmail.com');
INSERT INTO Phones VALUES('briang', '7325762390');
INSERT INTO Credit_Cards VALUES('8819567890729451', 'Visa', 'Brian Golden', '2015-05-31');
INSERT INTO Has_Card VALUES('8819567890729451', 'briang');
INSERT INTO Addresses VALUES('123 Good St', '08873', 'Brian Golden', 'briang');

INSERT INTO Members VALUES('aaron', '$2a$08$2psg6otTrTfKCA2hCbTL4Omb43MZ4VrdyLdPxYmqUEfERIzWd/9H6'
                           /*'goodermuth'*/, NULL);
INSERT INTO Registered_Users VALUES('aaron', 'Aaron Goodermuth', 'M', '1990-09-12', 100000, 0);
INSERT INTO Emails VALUES('aaron', 'aaron@goodermuth.com');
INSERT INTO Phones VALUES('aaron', '8664389023');
INSERT INTO Credit_Cards VALUES('6285767890572905', 'Masetercard', 'Aaron Goodermuth', '2014-10-31');
INSERT INTO Has_Card VALUES('6285767890572905', 'aaron');
INSERT INTO Addresses VALUES('3 Sparks St', '16801', 'Aaron Goodermuth', 'aaron');

INSERT INTO Members VALUES('fitz', '$2a$08$lZvQOzcfW83hIAz5hoq2oerEaY.GK8PT9O6OhAdzBDpyWjIohzfz6'
                           /*'meow'*/, NULL);
INSERT INTO Registered_Users VALUES('fitz', 'Mike Fitz', 'M', '1991-10-20', 120001, 0);
INSERT INTO Emails VALUES('fitz', 'mike@fitz.com');
INSERT INTO Phones VALUES('fitz', '9087464352');
INSERT INTO Credit_Cards VALUES('5247197890570091', 'American Express', 'Mike Fitz', '2016-12-31');
INSERT INTO Has_Card VALUES('5247197890570091', 'fitz');
INSERT INTO Addresses VALUES('24 Barnard St', '16801', 'Mike Fitz', 'fitz');

INSERT INTO Members VALUES('nickd', '$2a$08$D0zlzRUNj5OXttydHRcjw.bw3i5FtG3JvX8dqAJsGH..rBmSUrJAK'
                           /*'doyle'*/, NULL);
INSERT INTO Registered_Users VALUES('nickd', 'Nick Doyle', 'M', '1992-04-20', 120000, 0);
INSERT INTO Emails VALUES('nickd', 'nick@doyle.com');
INSERT INTO Phones VALUES('nickd', '7157778321');
INSERT INTO Credit_Cards VALUES('9801740947625387', 'American Express', 'Nick Doyle', '2015-12-31');
INSERT INTO Has_Card VALUES('9801740947625387', 'nickd');
INSERT INTO Addresses VALUES('29 N. Barnard St', '16801', 'Nick Doyle', 'nickd');

INSERT INTO Members VALUES('johnhannan', '$2a$08$dHhbqziWY.za.odDWvGli.uoPy3zAkK/g9h7ekN7f8thKVH8rl6/C'
                           /*'hannan'*/, NULL);
INSERT INTO Registered_Users VALUES('johnhannan', 'John Hannan', 'M', '1980-02-09', 140000, 0);
INSERT INTO Emails VALUES('johnhannan', 'hannan@cse.com');
INSERT INTO Phones VALUES('johnhannan', '8147798321');
INSERT INTO Credit_Cards VALUES('9801740947111087', 'American Express', 'John Hannan', '2015-12-31');
INSERT INTO Has_Card VALUES('9801740947111087', 'johnhannan');
INSERT INTO Addresses VALUES('110 Pugh St', '16801', 'John Hannan', 'johnhannan');

INSERT INTO Members VALUES('drlee', '$2a$08$qKox9FnMVvWYakxlQaJtNeb5YM1ra1su6mrGoczYNtvczfqvFuyv6'
                           /*'database'*/, NULL);
INSERT INTO Registered_Users VALUES('drlee', 'Wang-Chien Lee', 'M', '1978-03-12', 130000, 0);
INSERT INTO Emails VALUES('drlee', 'drlee@cse.com');
INSERT INTO Phones VALUES('drlee', '8148890012');
INSERT INTO Credit_Cards VALUES('7772740947111087', 'Visa', 'Wang-Chien Lee', '2015-11-30');
INSERT INTO Has_Card VALUES('7772740947111087', 'drlee');
INSERT INTO Addresses VALUES('114 Pugh St', '16801', 'Wang-Chien Lee', 'drlee');

INSERT INTO Members VALUES('thedon', '$2a$08$wxz3y1zCiAXqR18.pi0wBe/tpC6XtfGgbOqlDfH0qha3QOFnzIWfC'
                           /*'dhellz'*/, NULL);
INSERT INTO Registered_Users VALUES('thedon', 'Don Heller', 'M', '1940-05-02', 133700, 0);
INSERT INTO Emails VALUES('thedon', 'thedon@cse.com');
INSERT INTO Phones VALUES('thedon', '8144387611');
INSERT INTO Credit_Cards VALUES('9384700195578321', 'Visa', 'Don Heller', '2015-11-30');
INSERT INTO Has_Card VALUES('9384700195578321', 'thedon');
INSERT INTO Addresses VALUES('78 S. Garner St', '16801', 'Don Heller', 'thedon');

INSERT INTO Members VALUES('doughogan', '$2a$08$dAnO5a8Kw2FD5ekAwJM2wuqISQ70Jz5NV/UfYfoFdzSD/LBBOLkVm'
                           /*'discretemath'*/, NULL);
INSERT INTO Registered_Users VALUES('doughogan', 'Doug Hogan', 'M', '1981-07-11', 110000, 0);
INSERT INTO Emails VALUES('doughogan', 'doughogan@cse.com');
INSERT INTO Phones VALUES('doughogan', '8149980001');
INSERT INTO Credit_Cards VALUES('9384700199912387', 'Visa', 'Doug Hogan', '2015-11-30');
INSERT INTO Has_Card VALUES('9384700199912387', 'doughogan');
INSERT INTO Addresses VALUES('11 Burrowes St', '16801', 'Doug Hogan', 'doughogan');

INSERT INTO Members VALUES('zachsloane', '$2a$08$gFVxCihX92ogFqUmaKfHhuUPepKQeBsk2abwc.9K5troWNhfIFpim'
                           /*'jscrollpane'*/, NULL);
INSERT INTO Registered_Users VALUES('zachsloane', 'Zach Sloane', 'M', '1992-07-11', 4000, 1300);
INSERT INTO Emails VALUES('zachsloane', 'zach@sloane.com');
INSERT INTO Phones VALUES('zachsloane', '4781109855');
INSERT INTO Credit_Cards VALUES('8873609228172983', 'Visa', 'Zach Sloane', '2014-12-31');
INSERT INTO Has_Card VALUES('8873609228172983', 'zachsloane');
INSERT INTO Addresses VALUES('12 Pollock Rd', '16802', 'Zach Sloane', 'zachsloane');

INSERT INTO Members VALUES('alexschneider', '$2a$08$IKOFo3zKsm2HVMOiEdECb.wPf99Rmx5HgqxFLtgWVgwPncVRSyZQa'
                           /*'thebigo'*/, NULL);
INSERT INTO Registered_Users VALUES('alexschneider', 'Alex Schneider', 'M', '1992-06-28', 100000, 1150);
INSERT INTO Emails VALUES('alexschneider', 'alex@schneider.com');
INSERT INTO Phones VALUES('alexschneider', '6720092187');
INSERT INTO Credit_Cards VALUES('6572984009174562', 'American Express', 'Alex Schneider', '2015-06-30');
INSERT INTO Has_Card VALUES('6572984009174562', 'alexschneider');
INSERT INTO Addresses VALUES('88 Main St', '21201', 'Alex Schneider', 'alexschneider');

INSERT INTO Members VALUES('alexeliasof', '$2a$08$FuPfvXu48hoC7Ti6O0Hj0edbOSISwipMX42aHnfn67jApNKnuFAO2'
                           /*'commissioner'*/, NULL);
INSERT INTO Registered_Users VALUES('alexeliasof', 'Alex Eliasof', 'M', '1992-02-28', 90000, 0);
INSERT INTO Emails VALUES('alexeliasof', 'alex@eliasof.com');
INSERT INTO Phones VALUES('alexeliasof', '7219903561');
INSERT INTO Credit_Cards VALUES('1167902755610982', 'Mastercard', 'Alex Eliasof', '2015-04-30');
INSERT INTO Has_Card VALUES('1167902755610982', 'alexeliasof');
INSERT INTO Addresses VALUES('90 Main St', '08807', 'Alex Eliasof', 'alexeliasof');

INSERT INTO Members VALUES('davidmarchiori', '$2a$08$Ay4n0b9s/nWDEsKS706y7eSJaT.KYN/RcawuQHx6urwcvYj3RnoEO'
                           /*'marchiori'*/, NULL);
INSERT INTO Registered_Users VALUES('davidmarchiori', 'David Marchiori', 'M', '1992-04-28', 95000, 0);
INSERT INTO Emails VALUES('davidmarchiori', 'david@marchiori.com');
INSERT INTO Phones VALUES('davidmarchiori', '6509876210');
INSERT INTO Credit_Cards VALUES('4738291084756101', 'Mastercard', 'David Marchiori', '2015-04-30');
INSERT INTO Has_Card VALUES('4738291084756101', 'davidmarchiori');
INSERT INTO Addresses VALUES('34 Cedar Grove Ln', '96162', 'David Marchiori', 'davidmarchiori');

INSERT INTO Members VALUES('rickytrent', '$2a$08$VkOrxRGfZKvGJYcOIHeh4eMF61mW/wZYVkmHfWJVI9RBYE1AYYEGm'
                           /*'rutgers'*/, NULL);
INSERT INTO Registered_Users VALUES('rickytrent', 'Ricky Trent', 'M', '1987-02-13', 99000, 0);
INSERT INTO Emails VALUES('rickytrent', 'ricky@trent.com');
INSERT INTO Phones VALUES('rickytrent', '7891107810');
INSERT INTO Credit_Cards VALUES('8593752395275205', 'Visa', 'Ricky Trent', '2015-09-30');
INSERT INTO Has_Card VALUES('8593752395275205', 'rickytrent');
INSERT INTO Addresses VALUES('16 3rd Avenue', '10465', 'Ricky Trent', 'rickytrent');

INSERT INTO Members VALUES('mattylight', '$2a$08$EP3KpVykX.Lq.gSr4I18qeawsU0c5Hny7K1RY/b/XbXmEgG6FnMke'
                           /*'youngtombrady'*/, NULL);
INSERT INTO Registered_Users VALUES('mattylight', 'Matt McGloin', 'M', '1990-08-20', 80000, 0);
INSERT INTO Emails VALUES('mattylight', 'matt@psufootball.com');
INSERT INTO Phones VALUES('mattylight', '6109984721');
INSERT INTO Credit_Cards VALUES('84291234038123515', 'American Express', 'Matt McGloin', '2015-09-31');
INSERT INTO Has_Card VALUES('84291234038123515', 'mattylight');
INSERT INTO Addresses VALUES('222 Hemlock Dr', '18966', 'Matt McGloin', 'mattylight');

INSERT INTO Members VALUES('bobfb', '$2a$08$5UHds/mwkcVlCtzWdFc/mOo1x30L6Jfo2D0Gi/cJMVvrbAdGEK5Iy'
                           /*'football'*/, NULL);
INSERT INTO Registered_Users VALUES('bobfb', "Bill O'Brian", 'M', '1978-11-15', 130000, 0);
INSERT INTO Emails VALUES('bobfb', 'bill@psufootball.com');
INSERT INTO Phones VALUES('bobfb', '8148885412');
INSERT INTO Credit_Cards VALUES('5234655612375512', 'Visa', "Bill O'Brien", '2015-09-30');
INSERT INTO Has_Card VALUES('5234655612375512', 'bobfb');
INSERT INTO Addresses VALUES('76 Park Avenue', '16802', "Bill O'Brian", 'bobfb');

/* ***** Ratings ***** */
/* (rated_user, rating_user) */
INSERT INTO Can_Rate VALUES ('briang', 'nickd', 14);
INSERT INTO Can_Rate VALUES ('briang', 'johnhannan', 15);
INSERT INTO Can_Rate VALUES ('nickd', 'fitz', 16);
INSERT INTO Can_Rate VALUES ('thedon', 'drlee', 17);
INSERT INTO Can_Rate VALUES ('thedon', 'aaron', 18);
INSERT INTO Can_Rate VALUES ('aaron', 'nickd', 19);
INSERT INTO Can_Rate VALUES ('aaron', 'fitz', 20);
INSERT INTO Can_Rate VALUES ('bobfb', 'mattylight', 21);
INSERT INTO Can_Rate VALUES ('bobfb', 'alexeliasof', 22);

/* ***** Ratings ***** */
/* (rated_user, rating_user, rating, review_date DATETIME, item_id INT, review_description) */
INSERT INTO Ratings VALUES ('briang', 'nickd', 7, '2013-03-07', 14, 'Very accomodating');
INSERT INTO Ratings VALUES ('briang', 'johnhannan', 8, '2013-03-07', 15, 'Great seller, no deception');
INSERT INTO Ratings VALUES ('nickd', 'fitz', 9, '2013-03-07', 16, 'Promptly shipped');
INSERT INTO Ratings VALUES ('thedon', 'drlee', 9, '2013-03-07', 17, 'Condition as promised');
INSERT INTO Ratings VALUES ('thedon', 'aaron', 10, '2013-03-07', 18, 'Product was good, beard even better');
INSERT INTO Ratings VALUES ('aaron', 'nickd', 5, '2013-03-07', 19, 'Meh, just ok.');
INSERT INTO Ratings VALUES ('aaron', 'fitz', 9, '2013-03-07', 20, 'Thanks for the great item');
INSERT INTO Ratings VALUES ('bobfb', 'mattylight', 10, '2013-03-07', 21, 'Great playbook, I really liked it');
INSERT INTO Ratings VALUES ('bobfb', 'alexeliasof', 8, '2013-03-07', 22, 'Nice coaching job this season');

/* ***** Products ***** */
/* (product_id, p_name, p_desc, category_id) */
INSERT INTO Products VALUES  (0, "Craftsman C3 19.2-Volt Cordless 1/2' Impact Wrench Kit", 'This is a drill.', 6);
INSERT INTO Products VALUES  (1, 'Apple TV (3rd Generation)', 'This is an upgrade from the 2nd generation', 21);
INSERT INTO Products VALUES  (2, '2010 Claar Cellars Riesling (750 mL)', 'Tastes like wine', 26);
INSERT INTO Products VALUES  (3, 'Pop-Tarts Toaster Pastries Variety Pack, 48-Count Pastries', 'CONTAINS WHEAT AND SOY INGREDIENTS', 25);
INSERT INTO Products VALUES  (4, 'The Round House', 'Written by Louise Erdrich', 1);
INSERT INTO Products VALUES  (5, "Lambert Kay Fresh'n Clean Scented Dog Shampoo, 18-Ounce", 'Makes dogs clean', 22);
INSERT INTO Products VALUES  (6, 'StarCraft II: Heart of the Swarm Expansion Pack', 'So much Zerg doe', 20);
INSERT INTO Products VALUES  (7, 'Soylent Green', "IT'S PEOPLE", 1);
INSERT INTO Products VALUES  (8, 'Squier by Fender Mini Guitar, Torino Red', 'Learn to play a small guitar', 3);
INSERT INTO Products VALUES  (9, 'Fuzzy Black Bear Paw Slippers for Men and Women', 'Like having bears on your feet but less painful', 13);
INSERT INTO Products VALUES (10, 'Samsung 26 Cu. Ft. Stainless Steel French Door Refrigerator - RF4267HARS', 'Keeps stuff cold', 21);
INSERT INTO Products VALUES (11, 'Garmin nuvi 50 5-inch Portable GPS Navigator (US)', 'Never get lost again!', 3);
INSERT INTO Products VALUES (12, "Citizen Men's BM7170-53L Eco-Drive Titanium Watch", 'What time is it?', 14);


/* ***** Sale Items ***** */
/* (item_id, item_desc, product_id, username, scondition, url, posted_date, shipping_zip) */
INSERT INTO Sale_Items VALUES  (0, 'drill for sale!', 0, 'boyscout', 0, 'http://www.craftsman.com/', 
	'2013-03-20 09:15:00', '16802');
INSERT INTO Sale_Items VALUES  (1, 'this is a better drill, trust me', 0, 'briang', 0, 'http://www.craftsman.com/', 
	'2013-03-21 11:27:00', '08873');
INSERT INTO Sale_Items VALUES  (2, 'Drill from Home Depot', 0, 'homedepot', 0, 'http://noooooooooode.js/', '2013-04-28 23:04:00', '17543');
INSERT INTO Sale_Items VALUES  (3, 'Magical drill', 0, 'homedepot', 0, 'http://nooooooooooooooo.com/', '2013-04-28 23:10:00', '17557');	
INSERT INTO Sale_Items VALUES  (4, 'Pop-Tarts, slightly used', 3, 'aaron', 4, 'http://www.planarity.net/', '2013-03-07 10:10:10', '16801');
INSERT INTO Sale_Items VALUES  (5, 'Bear Slippers', 9, 'bobfb', 1, 'http://www.gopsusports.net/', '2013-03-22 11:59:59', '16802');
INSERT INTO Sale_Items VALUES  (6, 'Total nutrition food', 7, 'homedepot', 0, 'http://www.reddit.net/', '2013-03-20 03:13:37', '16803');
INSERT INTO Sale_Items VALUES  (7, 'Who wants this dog?', 5, 'fitz', 0, 'http://www.dog.com/', '2013-01-13 08:00:00', '16801');
INSERT INTO Sale_Items VALUES  (8, 'Super-comfy Slippers', 9, 'nickd', 2, 'http://arpa.net/', '2013-03-24 01:00:00', '18966');
INSERT INTO Sale_Items VALUES  (9, 'New Starcraft 2 Expansion Pack!!!', 6, 'rickytrent', 0, 'http://www.google.com/', '2013-03-14 09:10:00', '10465');
INSERT INTO Sale_Items VALUES (10, 'Samsung Fridge-freezer combo', 10, 'rickytrent', 2, 'http://espn.go.com/', '2013-03-17 19:00:00', '10465');
INSERT INTO Sale_Items VALUES (11, 'My favorite book ever!', 4, 'aaron', 3, 'http://broders.com/', '2013-02-13 11:30:00', '16801');
INSERT INTO Sale_Items VALUES (12, 'Broken Apple TV', 1, 'rickytrent', 5, 'http://apple.com/', '2013-03-17 19:00:00', '10465');
INSERT INTO Sale_Items VALUES (13, 'Used GPS', 11, 'aaron', 0, 'http://garmin.nuvi/', '2013-01-30 04:42:00', '16801');
INSERT INTO Sale_Items VALUES (14, 'Used GPS', 11, 'aaron', 2, 'http://garmin.nuvi/', '2013-02-15 04:42:00', '16801');
INSERT INTO Sale_Items VALUES (15, 'Used GPS', 11, 'aaron', 2, 'http://garmin.nuvi/', '2013-03-30 04:42:00', '16801');
INSERT INTO Sale_Items VALUES (16, 'Total nutrition food', 7, 'homedepot', 0, 'http://www.reddit.net/', '2013-03-20 03:13:37', '16803');
INSERT INTO Sale_Items VALUES (17, 'Who wants this dog?', 5, 'fitz', 0, 'http://www.dog.com/', '2013-01-13 08:00:00', '16801');
INSERT INTO Sale_Items VALUES (18, 'Super-comfy Slippers', 9, 'nickd', 2, 'http://arpa.net/', '2013-03-24 01:00:00', '18966');
INSERT INTO Sale_Items VALUES (19, 'New Starcraft 2 Expansion Pack!!!', 6, 'rickytrent', 0, 'http://www.google.com/', '2013-03-14 09:10:00', '10465');
INSERT INTO Sale_Items VALUES (20, 'Samsung Fridge-freezer combo', 10, 'rickytrent', 2, 'http://espn.go.com/', '2013-03-17 19:00:00', '10465');
INSERT INTO Sale_Items VALUES (21, 'My favorite book ever!', 4, 'aaron', 3, 'http://broders.com/', '2013-02-13 11:30:00', '16801');
INSERT INTO Sale_Items VALUES (22, 'Brand New Apple TV', 1, 'rickytrent', 0, 'http://apple.com/', '2013-03-17 19:00:00', '10465');

/* ***** Auctions ***** */
/* (item_id, reserve_price, recent_bid, end_date, recent_bidder) */
INSERT INTO Auctions VALUES  (1,  200,  145, '2013-04-04 11:27:00', 'aaron');
INSERT INTO Auctions VALUES  (3, 9999,   50, '2013-05-15 12:30:00', 'fitz');
INSERT INTO Auctions VALUES  (4,  100,  500, '2013-03-21 10:10:10', 'mattylight');
INSERT INTO Auctions VALUES  (5,  1000,  500, '2013-04-16 10:10:10', NULL);
INSERT INTO Auctions VALUES  (6, 1000,  3000, '2013-03-24 11:11:00', NULL);
INSERT INTO Auctions VALUES  (8,  800,  200, '2013-04-07 01:00:00', 'drlee');
INSERT INTO Auctions VALUES (12,  500,  300, '2013-04-07 01:00:00', 'aaron');
INSERT INTO Auctions VALUES (13, 2000, 1800, '2013-04-07 01:00:00', 'nickd');
INSERT INTO Auctions VALUES (15, 3000, 1800, '2013-04-16 01:00:00', NULL);
INSERT INTO Auctions VALUES (19, 4000, 750, '2013-04-16 01:00:00', NULL);
INSERT INTO Auctions VALUES (20, 3000, 900, '2013-04-16 01:00:00', NULL);
INSERT INTO Auctions VALUES (22, 7000, 1000, '2013-04-16 01:00:00', 'fitz');

INSERT INTO Has_Bid VALUES (1, 145, 'aaron');
INSERT INTO Has_Bid VALUES (3, 50, 'fitz');
INSERT INTO Has_Bid VALUES (4, 500, 'mattylight');
INSERT INTO Has_Bid VALUES (8, 800, 'drlee');
INSERT INTO Has_Bid VALUES (12, 300, 'aaron');
INSERT INTO Has_Bid VALUES (13, 1500, 'briang');
INSERT INTO Has_Bid VALUES (13, 1800, 'nickd');
INSERT INTO Has_Bid VALUES (22, 700, 'alexeliasof');
INSERT INTO Has_Bid VALUES (22, 1000, 'fitz');

/* ***** Sales ***** */
/* (item_id, price, purchased_user) */
INSERT INTO Sales  VALUES  (0, 98000);
INSERT INTO Sales  VALUES  (2,  1200);
INSERT INTO Sales  VALUES  (7,  2000);
INSERT INTO Sales  VALUES  (9,  1500);
INSERT INTO Sales  VALUES (10, 20000);
INSERT INTO Sales  VALUES (11,  1000);
INSERT INTO Sales  VALUES (17,  5500);
INSERT INTO Sales  VALUES (18,  4000);
INSERT INTO Sales  VALUES (21,  800);

/* ***** Orders ***** */
/* (item_id, username, status, action_date, price, street, zip, cnumber) */
INSERT INTO Orders VALUES (14, 'johnhannan', 0, '2013-04-17', 6000, 
                           '110 Pugh St', '16801', '9801740947111087');
INSERT INTO Orders VALUES (16, 'drlee', 0, '2013-04-17', 1200, 
                           '114 Pugh St', '16801', '7772740947111087');

/* ***** Admin Users ***** */
INSERT INTO Admin_Users VALUES('admin', 'admin', 1, 0);
INSERT INTO Admin_Users VALUES('test', 'test', 1, 0);


/* ***** Gift Cards ***** */
INSERT INTO Gift_Cards VALUES('12345', '1000');
INSERT INTO Gift_Cards VALUES('67890', '2000');
