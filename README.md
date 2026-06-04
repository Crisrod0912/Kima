# 📋 Kima

This project focuses on developing a web application for the Costa Rican company R.A. Costa Rica, a company specialized in regulatory affairs, registrations, and permit management at both national and international levels. The platform aims to centralize and automate key business processes, improving document organization, project tracking, and internal workflow management. Through an integrated environment, users will be able to manage clients, quotations, requirements, products, and project tickets, while also benefiting from automated file conversion and reporting tools. The main objective is to increase productivity, reduce operational errors, improve project traceability, and provide a scalable solution capable of supporting the company's future growth.

## ✨ Features

- 📢 **Announcements:** Allows the publication of internal and external announcements, improving communication between different users within the organization.
- 📇 **Contacts Management:** Enables the registration, organization, and consultation of important contacts such as suppliers, collaborators, and strategic partners.
- 👥 **Client Management:** Provides a centralized space for maintaining client information, facilitating project follow-up and customer relationship management.
- 🎫 **Ticket Tracking System:** Supports the creation and monitoring of incidents, requests, and project-related tasks, ensuring better traceability and workflow control.
- 💰 **Quotation Management:** Streamlines the creation and administration of quotations, helping improve sales and negotiation processes.
- 📑 **Requirements Management:** Records and tracks project requirements to ensure compliance throughout the development lifecycle.
- 💲 **Price List Administration:** Maintains updated product and service pricing information to support quotations and business operations.
- 👤 **User & Role Management:** Controls user access permissions and role assignments, enhancing system security and customization.
- 📊 **Reports and Monitoring:** Generates reports and provides visibility into project progress, operational performance, and business activities.
- 🔄 **Automatic File Conversion:** Automates document conversion processes, reducing manual work and improving efficiency.

## 🖥️ Technologies Used

- 🎨 **Frontend:** CSS, HTML, Javascript, SCSS
- 💻 **Backend:** PHP
- 🧱 **Framework**: Bootstrap
- 📚 **Libraries**: Dompdf, html5-php, JQuery, PHP CSS Parser, php-font-lib, php-svg-lib
- 🗄️ **Database:** Microsoft SQL Server
- 🌐 **Server**: Apache
- 🧩 **Version Control:** Git

## ⚙️ Installation

### 🧰 Prerequisites

To run this project locally, you'll need to have the following installed:

- 🌍 A web browser (e.g., Firefox, Google Chrome, Microsoft Edge)
- 🛢️ [SQL Server Management Studio 2022](https://learn.microsoft.com/en-us/ssms/install/install) (database manager)
- 💻 [VSCode](https://code.visualstudio.com/) (open source code editor)
- 🚀 [XAMPP](https://www.apachefriends.org/es/index.html) (includes PHP, and Apache)

### 🔧 Setup

1. 📥 Clone the repository:

    ```bash
    git clone https://github.com/Crisrod0912/Kima.git
    ```

2. 🔐 Configure database access:

   - Open **SQL Server Management Studio**.
   - Create a login in SQL Server that will be used to manage the project.
   
   Run the following commands in SQL Server Management Studio console:
   
   ```sql
   CREATE LOGIN kima WITH PASSWORD = 'your_password_here';
   ```

3. 🗄️ Configure database:

   - Create a new database called `Kima`.
   - Import the provided SQL file `Kima.sql` into the `Kima` database using your server.

4. ⚙️ Configure the project:

   - Update the database connection settings in the `database.php` file:

   ```php
    <?php
    $serverName = "your_server_here";
    $database = "Kima";
    $username = "kima";
    $password = "your_password_here";

    try {
        $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error de conexión a la base de datos: " . $e->getMessage());
    }
    ?>
   ```
   
   Ensure the SQL Server credentials and database name match your local setup.

5. ▶️ Start the XAMPP server:

   - Open the **XAMPP Control Panel** and click on "Start" for Apache.

6. 🌐 Access the platform by navigating to `http://localhost/Kima/` in your browser.

> [!NOTE]
> **Project Owner / Developer** 👨🏻‍💻  
>- Cristopher Rodríguez Fernández 
***
