# BookShop Project

## Overview

This project is a BookShop application that manages sales, customers, and products. It allows users to search for sales data with various filters such as customer name, product name, and product price. The application also provides pagination for easy navigation through the results.

## Features

- **Search Functionality**: Users can search for sales data based on customer name, product name, and product price.
- **Pagination**: Results are paginated to enhance user experience.
- **Total Price Calculation**: The total price of all filtered entries is displayed.
- **Database Connection**: The application connects to a MySQL database to retrieve and display data.

## Getting Started

1. **Database Setup**: Make sure to set up your MySQL database and update the connection details in the code.
2. **Clone the Repository**: Clone this repository to your local machine.
   ```bash
   git clone https://github.com/your-username/BookShop.git
3. Use dbschema.txt to create the tables
4. Run following code to import data from saledate.json to the database
5. ```bash
   php importdata.php
