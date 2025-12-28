---
description: Share the application on the local network
---

To allow other devices on your local network to access the site, follow these steps:

1.  **Stop the current development server**:
    - Go to the terminal where `npm run dev` is running.
    - Press `Ctrl + C` to stop it.

2.  **Run Vite with the host flag**:
    - Run the following command:
      ```powershell
      npm run dev -- --host
      ```
    - This exposes the Vite server to your local network.

3.  **Find your Local IP Address**:
    - Open a new terminal and run:
      ```powershell
      ipconfig
      ```
    - Look for the **IPv4 Address** (usually starts with `192.168.x.x` or `10.x.x.x`).

4.  **Start the Backend Server**:
    - Open a **new** terminal window (keep the `npm run dev` one running).
    - Run:
      ```powershell
      php artisan serve --host 0.0.0.0
      ```
    - This exposes the Laravel application to your network.

5.  **Access from other devices**:
    - On your phone or another computer, enter:
      ```
      http://<YOUR_IP_ADDRESS>:8000
      ```
    - Example: `http://10.10.3.19:8000`
    - **Important**: Do NOT use port 5173. That is only for assets (styles/scripts). You must access port 8000.

6.  **Troubleshooting**:
    - If the page loads but styles are missing, ensure `npm run dev -- --host` is still running.
    - Check your Windows Firewall to allow connections to PHP and Node.js.
