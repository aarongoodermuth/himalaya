INSERT INTO Admin_Users VALUES("admin", "admin", "1", "0");
INSERT INTO Admin_Users VALUES("test", "test", "1", "0");

INSERT INTO Gift_Cards VALUES("12345", "1000");
INSERT INTO Gift_Cards VALUES("67890", "2000");

INSERT INTO Categories VALUES ("1", "Home Improvement", "1");

INSERT INTO Members VALUES("homedepot", "supplier", NULL);
INSERT INTO Suppliers VALUES("homedepot", "Home Depot", "John Smith");
INSERT INTO Emails VALUES("homedepot", "sales@homedepot.com");
INSERT INTO Phones VALUES("homedepot", "1800466333768");
INSERT INTO Addresses VALUES("2615 Green Tech Drive", "16803", "homedepot");
INSERT INTO Sells_In VALUES("homedepot", "1");

INSERT INTO Members VALUES("boyscout", "normalform", NULL);
INSERT INTO Registered_Users VALUES("boyscout", "Boyce-Cott", "M", "30", "1000000", "0");
INSERT INTO Emails VALUES("boyscout", "betterthan@3nf.org");
INSERT INTO Phones VALUES("boyscout", "3141592645");
INSERT INTO Credit_Cards VALUES("1234567890123456", "Visa", "2014-12-31");
INSERT INTO Has_Card VALUES("1234567890123456", "boyscout");
