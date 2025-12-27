  <h1 align="center">Direct Finds 🛍️</h1>
 <h1>https://webshop-3x5q.onrender.com/front-end/</h1>

---

## About The Project

**Direct Finds** is a front-end single-page application (SPA) that provides the user interface for a modern peer-to-peer marketplace. It allows users to browse products, view seller shops, and, for registered users, list their own items for sale.

This project was built from the ground up using fundamental web technologies to demonstrate solid programming principles without reliance on heavy frameworks. The single-page application experience is powered by the lightweight `spapp` jQuery library for smooth, client-side routing.

<br>

## DATABASE SCHEMA

<p align="center">
<img width="627" height="662" alt="image" src="https://github.com/user-attachments/assets/2f0e8e19-0ddc-446c-82ef-7060b683406f" /><br>
</p>

This database schema powers a full-featured e-commerce web application. It allows users to register, manage their profiles, and interact with products through carts and orders. Each product belongs to a category, and users can add items to their cart before completing a purchase. Orders track all necessary details like product quantities, total amount, shipping address, and purchase-time pricing. The schema supports essential e-commerce functionality such as product management, cart operations, and order processing.

## Features

✨ **General Features:**

- **Dynamic Product Feed:** Browse a grid of all available products.
- **Single Page Application:** Seamless navigation between pages without full reloads.
- **View Product Details:** Click on any item to see more information.
- **Explore Seller Shops:** Visit a seller's dedicated page to see all their listings.
- **Responsive Design:** Fully functional and looks great on desktop, tablet, and mobile devices.

👤 **User Account Features (Requires Backend):**

- **Create Account & Login:** Secure user authentication flow.
- **Post New Products:** An intuitive form for users to upload and list their items.
- **Manage Personal Shop:** Users get their own shop page that they can manage.
- **Edit/Delete Listings:** Full control over their own product posts.
- **Buy Items:** A simple purchasing workflow.

<br>

## Tech Stack

This project is built with a focus on simplicity, performance, and core web technologies.

- **HTML5:** For the structure and content of the application.
- **CSS3:** For custom styling and animations.
- **Bootstrap 5:** For a responsive grid system, pre-styled components, and a mobile-first design.
- **Vanilla JavaScript (ES6+):** For all application logic, DOM manipulation, and API interactions.
- **jQuery:** Used primarily for the `spapp` library to enable client-side routing.

<br>

## Getting Started

To get a local copy of the frontend up and running, follow these simple steps.

### Prerequisites

You only need a modern web browser and a code editor. For the best experience, using a live server is recommended.

### Installation

1.  **Clone the repository**
    ```sh
    git clone https://github.com/Anel314/webShop.git
    ```
2.  **Navigate to the project directory**
    ```sh
    cd webShop
    ```
3.  **Run the application**

    - **Recommended Method (with VS Code Live Server):**
      1.  Open the project folder in VS Code.
      2.  Right-click on the `front-end/index.html` file and select "Open with Live Server".
    - **Alternative Method:**
      Simply open the `index.html` file directly in your web browser.

<br>

<h2>Notice:</h2>
<p>All data for shops and products is currently loaded from local .json files just for the sake of displaying what to expect the site to look like, in further milestones this data will be dynamically loaded from the mySQL database.</p>

---

### MILESTONE 2

Inside the back-end folder you can find all the necessities for milestone2. <br>
Inside the database folder (back-end/rest/database) are 2 .sql files, the file names "webShopDump.sql" is the actual database that i dumped from DBeaver, but in case that file does not work for you i have created another file named
"inCaseDumpDoesNotWorkForYou.sql" that contains all the sql statements that i used to create my database. On my machine the database contains some dummydata that i used to test my dao layer on. If you need that as well i can provide that.

<h2>Notice</h2>
<p>I forgot that milestone2 was only for the dao layer so i kept working on the other layers too as you will notice in this version of the app. I have already implemented some Services and started the Routes i hope that does not make any trouble. I did structure the project in a way to let anybody know "where what is". So i hope you are able to find everything needed in here. Good luck grading!</p>

---
