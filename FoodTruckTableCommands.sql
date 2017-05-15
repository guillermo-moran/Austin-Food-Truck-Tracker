Create table Hours (
	hoursID int auto_increment not null,
   	openingTime varchar(40),
	closingTime varchar(40),
	daysOpen varchar(20),
	primary key(hoursID))
	Engine=InnoDB;

Create table FoodTruck (
	truckID int auto_increment not null,
   	truckName varchar(20),
    truckPhone varchar(20),
    truckEmail varchar(20),
	hoursID int,
	foreign key (hoursID) references Hours (hoursID) on delete cascade,
	primary key(truckID))
	Engine=InnoDB;

Create table Menu (
	menuID int auto_increment not null,
	menuItemName varchar(200),
	menuItemPrice decimal,
	truckID int,
	foreign key (truckID) references FoodTruck (truckID) on delete cascade,
	primary key(menuID))
	Engine=InnoDB;

Create table Location (
	locationID int auto_increment not null,
	locationAddress varchar(20),
	primary key(locationID))
	Engine=InnoDB;

Create table Employee (
	employeeID int auto_increment not null,
   	employeeName varchar(20),
	employeeDOB varchar(20),
	employeeAddress varchar(20),
	truckID int,
	foreign key (truckID) references FoodTruck (truckID) on delete cascade,
	primary key(employeeID))
	Engine=InnoDB;

Create table Customer (
	customerID int auto_increment not null,
	customerName varchar(20),
	customerEmail varchar(50),
	customerPass varchar(100),
	primary key(customerID))
	Engine=InnoDB;

Create table Rating (
	ratingID int auto_increment not null,
   	ratingNumber int,
	truckID int,
	foreign key (truckID) references FoodTruck (truckID) on delete cascade,
	primary key(ratingID))
	Engine=InnoDB;

Create table FoodTruckCustomer (
	customerID int,
	truckID int,
	foreign key (customerID) references Customer (customerID) on delete cascade,
	foreign key (truckID) references FoodTruck (truckID) on delete cascade,
	primary key(customerID, truckID))
	Engine=InnoDB;

Create table FoodTruckLocation (
	locationID int,
	truckID int,
	foreign key (locationID) references Location (locationID) on delete cascade,
	foreign key (truckID) references FoodTruck (truckID) on delete cascade,
	primary key(locationID, truckID))
	Engine=InnoDB;

Create table CustomerRating (
	customerID int,
	ratingID int,
	foreign key (customerID) references Customer (customerID) on delete cascade,
	foreign key (ratingID) references Rating (ratingID) on delete cascade,
	primary key(ratingID, customerID))
	Engine=InnoDB;
