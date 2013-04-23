/* C -> A */
CREATE TABLE Gift_Cards
(
	code VARCHAR(16) NOT NULL,
	amount SMALLINT NOT NULL,
	CONSTRAINT pk_code PRIMARY KEY (code)
);

/* U -> PTSE */
CREATE TABLE Admin_Users
(
	username VARCHAR(32) NOT NULL, 
	password VARCHAR(128) NOT NULL, 
	atype TINYINT NOT NULL,
	asession INT, 
	CONSTRAINT pk_username PRIMARY KEY (username)
);

/* Z -> CS(lat)(lon), (lat)(lon) -> S */
/* lat-long actually does not determine the ZIP, see Virgin Islands */
CREATE TABLE ZIP_Codes
(
	zip CHAR(5) NOT NULL,
	city VARCHAR(128) NOT NULL,
	zstate CHAR(2) NOT NULL,
	lat FLOAT NOT NULL,
	lon FLOAT NOT NULL,
	CONSTRAINT pk_zip PRIMARY KEY (zip)
);

/*  I -> NP */
CREATE TABLE Categories
(
	category_id SMALLINT NOT NULL,
	category_name VARCHAR(64) NOT NULL,
	parent_category_id SMALLINT NOT NULL,
	CONSTRAINT pk_category_id PRIMARY KEY (category_id),
	CONSTRAINT fk_parentcat FOREIGN KEY (parent_category_id) REFERENCES Categories (category_id) 
		ON DELETE CASCADE
);

/*  U -> P */
CREATE TABLE Members 
(
	username VARCHAR(32) NOT NULL, 
	password VARCHAR(128) NOT NULL,
	asession INT,
	CONSTRAINT pk_username PRIMARY KEY (username)
);

/* U-> CN */
CREATE TABLE Suppliers
(
	username VARCHAR(32) NOT NULL, 
	company_name VARCHAR(256) NOT NULL,
	contact_name VARCHAR(128), 
	CONSTRAINT pk_username PRIMARY KEY (username), 
	CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES Members (username)
		ON DELETE CASCADE  /* delete all info on this member/supplier */
);

/* I -> DCN */
CREATE TABLE Products
(
	product_id INT NOT NULL,
	p_name VARCHAR(128) NOT NULL,
	p_desc VARCHAR(500) NOT NULL,
	category_id SMALLINT NOT NULL,
	CONSTRAINT pk_product_id PRIMARY KEY (product_id),
	FOREIGN KEY (category_id) REFERENCES Categories(category_id)
		ON DELETE NO ACTION /* disallow deletion if products still exist in that category */
);

/* no FDs! */
CREATE TABLE Phones
(
	username VARCHAR(32) NOT NULL,
	pnum VARCHAR(16) NOT NULL,
	CONSTRAINT pk_username_pnum PRIMARY KEY (username, pnum),
	CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES Members (username) 
		ON DELETE CASCADE
);

/* I -> DPUCUSZ */
CREATE TABLE Sale_Items
(
	item_id INT NOT NULL,
	item_desc VARCHAR(500) NOT NULL,
	product_id INT NOT NULL,
	username VARCHAR(32) NOT NULL, 
	scondition TINYINT NOT NULL,  /* treat as enum, 0 = new, 1 = ... */
	url VARCHAR(256) NOT NULL,
	posted_date DATETIME NOT NULL,
	shipping_zip CHAR(5) NOT NULL,
	CHECK (scondition >= 0 AND scondition <= 5),
	CONSTRAINT pk_item_id PRIMARY KEY (item_id),
	CONSTRAINT fk_product_id FOREIGN KEY (product_id) REFERENCES Products (product_id)
		ON DELETE NO ACTION, /* disallow deletion if items of that type still exist */
	CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES Members (username)
		ON DELETE NO ACTION,
	CONSTRAINT fk_shipping_zip FOREIGN KEY (shipping_zip) REFERENCES ZIP_Codes (zip) 
		ON DELETE NO ACTION  /* a Sale_Item still references this ZIP */
);

/* no FDs! */
CREATE TABLE Addresses
(
	street VARCHAR(256) NOT NULL, 
	zip CHAR(5) NOT NULL, 
	name VARCHAR(128) NOT NULL,
	username VARCHAR(32) NOT NULL, 
	CONSTRAINT pk_street_zip_username PRIMARY KEY (street, zip, username), 
	CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES Members (username) 
		ON DELETE CASCADE,
	CONSTRAINT fk_zip FOREIGN KEY (zip) REFERENCES ZIP_Codes (zip) 
		ON DELETE NO ACTION
		ON UPDATE CASCADE
);

/* U -> E */
CREATE TABLE Emails
(
	username VARCHAR(32) NOT NULL, 
	email VARCHAR(128) NOT NULL, 
	CONSTRAINT pk_username PRIMARY KEY (username), 
	CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES Registered_Users (username)
		ON DELETE CASCADE
);

/* U -> NGAIB */
CREATE TABLE Registered_Users
(
	username VARCHAR(32) NOT NULL DEFAULT 'dummy', 
	name VARCHAR(128) NOT NULL,
	gender VARCHAR(16) NOT NULL,
	dob DATE NOT NULL,
	income INT NOT NULL,
	gift_card_balance INT NOT NULL,
	CONSTRAINT pk_username PRIMARY KEY (username),
	CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES Members (username) 
		ON DELETE CASCADE  /* delete all info on this member/reg user */
);

/*  I -> PREB */
CREATE TABLE Auctions
(
	item_id INT NOT NULL,
	reserve_price MEDIUMINT DEFAULT 0, 
	recent_bid MEDIUMINT DEFAULT 0,
	end_date DATETIME NOT NULL, 
	recent_bidder VARCHAR(32),
	CONSTRAINT pk_item_id PRIMARY KEY (item_id),
	CONSTRAINT fk_item_id FOREIGN KEY (item_id) REFERENCES Sale_Items (item_id)
		ON DELETE NO ACTION, 
	CONSTRAINT fk_recent_bidder FOREIGN KEY (recent_bidder) REFERENCES Registered_Users (username)
		ON DELETE NO ACTION
);

CREATE TABLE Has_Bid
(
	item_id INT NOT NULL, 
	bid_amount INT NOT NULL, 
	bidder VARCHAR(32) NOT NULL, 
	CONSTRAINT pk_item_bid PRIMARY KEY (item_id, bidder), 
	CONSTRAINT fk_item_id FOREIGN KEY (item_id) REFERENCES Auctions
		ON DELETE CASCADE,
	CONSTRAINT fk_bidder FOREIGN KEY (bidder) REFERENCES Registered_Users (username)
		ON DELETE CASCADE
);

/* I -> PU */
CREATE TABLE Sales
(
	item_id INT NOT NULL,
	price MEDIUMINT NOT NULL,
	CONSTRAINT pk_item_id PRIMARY KEY (item_id),
	CONSTRAINT fk_item_id FOREIGN KEY (item_id) REFERENCES Sale_Items (item_id)
		ON DELETE NO ACTION
);

/* N -> TE */
CREATE TABLE Credit_Cards
(
	cnumber VARCHAR(16) NOT NULL, 
	ctype VARCHAR(32) NOT NULL, 
	cname VARCHAR(128) NOT NULL,
	expiration DATE NOT NULL, 
	CONSTRAINT pk_cnumber PRIMARY KEY (cnumber)
);

/* no FDs! */
CREATE TABLE Has_Card
(
	cnumber VARCHAR(16) NOT NULL,
	username VARCHAR(32) NOT NULL,
	CONSTRAINT pk_cnumber_username PRIMARY KEY (cnumber, username),
	CONSTRAINT fk_cnumber FOREIGN KEY (cnumber) REFERENCES Credit_Cards (cnumber)
		ON DELETE CASCADE,
	CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES Registered_Users (username)
		ON DELETE CASCADE
);

/* no FDs */
CREATE TABLE Sells_In
(
	supplier VARCHAR(32) NOT NULL, 
	category_id SMALLINT NOT NULL,
	CONSTRAINT pk_supplier_categoryid PRIMARY KEY (supplier, category_id),
	CONSTRAINT fk_supplier FOREIGN KEY (supplier) REFERENCES Suppliers (username)
		ON DELETE CASCADE,
	CONSTRAINT fk_categoryid FOREIGN KEY (category_id) REFERENCES Categories (category_id)
		ON DELETE CASCADE
);

/* no FDs */
CREATE TABLE Can_Rate
(
	rated_user VARCHAR(32) NOT NULL, 
	rating_user VARCHAR(32) NOT NULL, 
	item_id INT NOT NULL, 
	CONSTRAINT pk_rated_user_rating_user PRIMARY KEY (rated_user, rating_user), 
	CONSTRAINT fk_rated_user FOREIGN KEY (rated_user) REFERENCES Members (username) 
		ON DELETE CASCADE,
	CONSTRAINT fk_rating_user FOREIGN KEY (rating_user) REFERENCES Registered_Users (username)
		ON DELETE CASCADE,
	CONSTRAINT fk_item_id FOREIGN KEY (item_id) REFERENCES Sale_Items (item_id)
		ON DELETE CASCADE
);

/*  (rated_user)(rating_user)(review_date) -> (rating)(item_id) */
CREATE TABLE Ratings
(
	rated_user VARCHAR(32) NOT NULL,
	rating_user VARCHAR(32),  /* may be null if the rating user delete's their account */
	rating TINYINT NOT NULL,
	review_date DATE NOT NULL,
	item_id INT,  /* may be null if item has been removed from the database */
	review_description VARCHAR(500) NOT NULL,
	CHECK (rating >= 1 AND rating <= 10),
	CONSTRAINT pk_rated_rating_date PRIMARY KEY (rated_user, rating_user, review_date),
	CONSTRAINT fk_rated_user FOREIGN KEY (rated_user) REFERENCES Registered_Users (username) 
		ON DELETE CASCADE,
	CONSTRAINT fk_rating_user FOREIGN KEY (rating_user) REFERENCES Registered_Users (username)
		ON DELETE SET NULL,
	CONSTRAINT fk_item_id FOREIGN KEY (item_id) REFERENCES Sale_Items (item_id)
		ON DELETE SET NULL
);

/* I -> USA */
CREATE TABLE Orders
(
	item_id INT NOT NULL, 
	username VARCHAR(32) NOT NULL, 
	status TINYINT NOT NULL, 
	action_date DATE NOT NULL, 
	price INT NOT NULL, 
	street VARCHAR(256) NOT NULL, 
	zip CHAR(5) NOT NULL, 
	cnumber VARCHAR(16) NOT NULL, 
	CHECK (status >= 0 AND status <= 4),
	CONSTRAINT pk_itemid PRIMARY KEY (item_id), 
	CONSTRAINT fk_itemid FOREIGN KEY (item_id) REFERENCES Sale_Items (item_id)
		ON DELETE NO ACTION, 
	CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES Registered_Users (username)
		ON DELETE SET DEFAULT, 
	CONSTRAINT fk_street FOREIGN KEY (street) REFERENCES Addresses (street)
		ON DELETE CASCADE,
	CONSTRAINT fk_zip FOREIGN KEY (zip) REFERENCES ZIP_Codes (zip)
		ON DELETE CASCADE,
	CONSTRAINT fk_cnumber FOREIGN KEY (cnumber) REFERENCES Credit_Cards (cnumber)
		ON DELETE CASCADE		
);
