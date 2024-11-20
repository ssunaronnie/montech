/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Roboto', sans-serif;
    color: #333;
    line-height: 1.6;
}

/* Navbar */
.navbar {
    background: #004aad; /* Blue */
    color: #fff;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.navbar .logo {
    font-size: 20px;
    font-weight: bold;
}

.navbar .nav-links a {
    color: #fff;
    text-decoration: none;
    margin: 0 15px;
    transition: color 0.3s;
}

.navbar .nav-links a:hover {
    color: #ffdd00; /* Yellow */
}

/* Hamburger Menu (Mobile View) */
.hamburger-menu {
    display: none;
    flex-direction: column;
    cursor: pointer;
}

.hamburger-menu span {
    background: #fff;
    height: 3px;
    width: 25px;
    margin: 4px;
    transition: all 0.3s;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-links {
        display: none;
    }

    .hamburger-menu {
        display: flex;
    }
}

/* Footer */
.footer {
    background: #004aad;
    color: #fff;
    text-align: center;
    padding: 15px 0;
}

/* Cards Section */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.card {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card:hover {
    border-color: #004aad;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transform: scale(1.05);
    transition: all 0.3s ease;
}
