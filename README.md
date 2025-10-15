  <h1 align="center">Direct Finds 🛍️</h1>

<details>
  <summary>Table of Contents</summary>
  <ol>
    <li><a href="#about-the-project">About The Project</a></li>
    <li><a href="#screenshots">Screenshots</a></li>
    <li><a href="#features">Features</a></li>
    <li><a href="#tech-stack">Tech Stack</a></li>
    <li><a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#project-structure">Project Structure</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>

---

## About The Project

**Direct Finds** is a front-end single-page application (SPA) that provides the user interface for a modern peer-to-peer marketplace. It allows users to browse products, view seller shops, and, for registered users, list their own items for sale.

This project was built from the ground up using fundamental web technologies to demonstrate solid programming principles without reliance on heavy frameworks. The single-page application experience is powered by the lightweight `spapp` jQuery library for smooth, client-side routing.

**Note:** This repository contains the **frontend code only**. A separate backend server is required to handle data, user authentication, and business logic.

<br>

## Screenshots

Here’s a sneak peek at the Direct Finds user interface.

|                            Homepage                            |                            Product Details                             |                           User's Shop                            |
| :------------------------------------------------------------: | :--------------------------------------------------------------------: | :--------------------------------------------------------------: |
| ![Homepage Screenshot]([link-to-your-homepage-screenshot.png]) | ![Product Page Screenshot]([link-to-your-product-page-screenshot.png]) | ![Shop Page Screenshot]([link-to-your-shop-page-screenshot.png]) |

_(Pro-tip: Create a short GIF showing the user flow and add it here!)_

<br>

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
- **[Font Awesome](https://fontawesome.com/)** (Optional): For icons throughout the application.

<br>

## Getting Started

To get a local copy of the frontend up and running, follow these simple steps.

### Prerequisites

You only need a modern web browser and a code editor. For the best experience, using a live server is recommended.

- A modern web browser (e.g., Google Chrome, Firefox).
- [VS Code](https://code.visualstudio.com/) with the **[Live Server](https://marketplace.visualstudio.com/items?itemName=ritwickdey.LiveServer)** extension is highly recommended.

### Installation

1.  **Clone the repository**
    ```sh
    git clone [https://github.com/](https://github.com/)[your-github-username]/[your-repo-name].git
    ```
2.  **Navigate to the project directory**
    ```sh
    cd [your-repo-name]
    ```
3.  **Run the application**

    - **Recommended Method (with VS Code Live Server):**
      1.  Open the project folder in VS Code.
      2.  Right-click on the `index.html` file and select "Open with Live Server".
    - **Alternative Method:**
      Simply open the `index.html` file directly in your web browser. _(Note: Some API requests might be blocked by CORS policy with this method.)_

4.  **Connect to the Backend**
    This frontend is designed to communicate with a backend API. You will need to:
    1.  Set up and run the backend server separately.
    2.  Configure the API's base URL in the frontend code. Look for a configuration file, such as `js/config.js` or a variable at the top of `js/api.js`, and update the URL to point to your running backend server (e.g., `http://localhost:3000/api`).

<br>

## Project Structure

The file structure is organized to be simple and intuitive:
