<style>

.navbar {
            background-color: black;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background-color: #000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
        }

        .nav-link {
            color: #fff !important;
            font-size: 1.2rem;
            margin-right: 15px;
            transition: color 0.3s, transform 0.3s;
        }

        .nav-link:hover {
            color: #ccc;
            transform: scale(1.1);
        }


    
</style>
