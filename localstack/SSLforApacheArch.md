# Setting Up SSL/TLS for Local Apache Server (Arch Linux & Ubuntu)

This guide walks through setting up HTTPS with trusted certificates for a local Apache server using mkcert, including access from other devices on your local network.

## Prerequisites
- Apache HTTP Server installed
- Terminal access with sudo privileges
- (Optional) A file transfer method (like KDE Connect or scp) for mobile devices

---

## PART 1: Initial Setup

### Step 1: Install mkcert
**On Arch Linux / CachyOS:**
sudo pacman -S mkcert

**On Ubuntu / Debian:**
sudo apt update
sudo apt install mkcert libnss3-tools
*(Note: libnss3-tools is required on Ubuntu for mkcert to install the local CA into Linux trust stores).*

### Step 2: Install the mkcert Certificate Authority
mkcert -install
*(This creates a local CA and installs it in your system and browser trust stores).*

### Step 3: Generate Certificates for localhost
⚠️ IMPORTANT: Run this WITHOUT sudo to match your user account!

mkcert localhost

*(This creates two files: `localhost.pem` and `localhost-key.pem`)*

### Step 4: Move Certificates to Apache Directory
**On Arch Linux / CachyOS:**
sudo mv localhost.pem localhost-key.pem /etc/httpd/conf/

**On Ubuntu / Debian:**
sudo mkdir -p /etc/apache2/ssl
sudo mv localhost.pem localhost-key.pem /etc/apache2/ssl/

**For Both:** Secure the private key permissions:
sudo chmod 600 /etc/httpd/conf/localhost-key.pem   # (Arch)
sudo chmod 600 /etc/apache2/ssl/localhost-key.pem  # (Ubuntu)

### Step 5: Enable Required Apache Modules
**On Arch Linux / CachyOS:**
Edit the main config file: `sudo nano /etc/httpd/conf/httpd.conf`
Uncomment (remove the `#`) from these lines:
  LoadModule ssl_module modules/mod_ssl.so
  LoadModule socache_shmcb_module modules/mod_socache_shmcb.so
  Include conf/extra/httpd-ssl.conf

**On Ubuntu / Debian:**
Ubuntu provides helper commands. Simply run:
  sudo a2enmod ssl
  sudo a2enmod socache_shmcb

### Step 6: Configure SSL Certificate Paths
**On Arch Linux / CachyOS:**
Edit: `sudo nano /etc/httpd/conf/extra/httpd-ssl.conf`
Update the paths to:
  SSLCertificateFile "/etc/httpd/conf/localhost.pem"
  SSLCertificateKeyFile "/etc/httpd/conf/localhost-key.pem"

**On Ubuntu / Debian:**
Edit: `sudo nano /etc/apache2/sites-available/default-ssl.conf`
Update the paths to:
  SSLCertificateFile      /etc/apache2/ssl/localhost.pem
  SSLCertificateKeyFile   /etc/apache2/ssl/localhost-key.pem
*(Then enable the site: `sudo a2ensite default-ssl.conf`)*

### Step 7: Restart Apache
**On Arch Linux / CachyOS:**
sudo systemctl restart httpd

**On Ubuntu / Debian:**
sudo systemctl restart apache2

### Step 8: Import Root CA into Desktop Browser (if needed)
If you still see certificate warnings on your computer:
1. Open Brave and navigate to: `brave://settings/certificates`
2. Click the "Authorities" tab, then "Import"
3. Navigate to `~/.local/share/mkcert/` (press Ctrl+H to show hidden folders)
4. Select `rootCA.pem`
5. Check "Trust this certificate for identifying websites" and click OK
6. Completely restart Brave

### Step 9: Test Your Secure Connection (Localhost)
Navigate to: `https://localhost`
You should see a secure padlock icon in the address bar.

---

## PART 2: Extended Setup (Accessing from Mobile/Local Network)

### Step 10: Generate Certificate for Local IP Address
First, find your computer's local IP address (e.g., `192.168.1.170`) using `ip a`.

Generate a new certificate that includes BOTH localhost and your IP address.
⚠️ IMPORTANT: Run this WITHOUT sudo!

mkcert localhost 192.168.1.170
*(Note: mkcert will name these `localhost+1.pem` and `localhost+1-key.pem` to avoid overwriting).*

### Step 11: Replace Apache Certificates
**On Arch Linux / CachyOS:**
sudo mv localhost+1.pem localhost+1-key.pem /etc/httpd/conf/
sudo mv /etc/httpd/conf/localhost+1.pem /etc/httpd/conf/localhost.pem
sudo mv /etc/httpd/conf/localhost+1-key.pem /etc/httpd/conf/localhost-key.pem

**On Ubuntu / Debian:**
sudo mv localhost+1.pem localhost+1-key.pem /etc/apache2/ssl/
sudo mv /etc/apache2/ssl/localhost+1.pem /etc/apache2/ssl/localhost.pem
sudo mv /etc/apache2/ssl/localhost+1-key.pem /etc/apache2/ssl/localhost-key.pem

**For Both:** Re-secure the private key:
sudo chmod 600 /etc/httpd/conf/localhost-key.pem   # (Arch)
sudo chmod 600 /etc/apache2/ssl/localhost-key.pem  # (Ubuntu)

### Step 12: Restart Apache
**Arch:** `sudo systemctl restart httpd`
**Ubuntu:** `sudo systemctl restart apache2`

### Step 13: Configure the Firewall (UFW)
Allow HTTPS traffic through the firewall:
sudo ufw allow 443/tcp

Verify the rule was added correctly (ensure it says `443`, not a typo like `433`):
sudo ufw status

### Step 14: Install Root CA on Mobile Device (Android Example)
Your phone needs to trust the mkcert CA to avoid security warnings.
1. Transfer `rootCA.pem` from `~/.local/share/mkcert/` on your computer to your phone (e.g., via KDE Connect).
2. On Android, go to: Settings > Security & privacy > More security settings > Encryption & credentials > Install a certificate > CA certificate.
3. Tap "Install anyway" if prompted.
4. Navigate to your Downloads folder and select `rootCA.pem`.
5. Name it (e.g., "mkcert") and tap OK.

### Step 15: Test Secure Connection on Mobile
Open your mobile browser and navigate to:
`https://<your-local-ip>` (e.g., `https://192.168.1.170`)

You should now see the secure padlock icon. Use an Incognito/Private tab if you previously visited the site and got cached errors.

---

## Troubleshooting

### Certificate Authority Mismatch (NET::ERR_CERT_AUTHORITY_INVALID)
Check who signed your certificate:
openssl x509 -in /etc/httpd/conf/localhost.pem -noout -issuer       # (Arch)
openssl x509 -in /etc/apache2/ssl/localhost.pem -noout -issuer      # (Ubuntu)

Make sure it matches the CA name in your browser's certificate store. If you ran `mkcert` with sudo, it created a different CA. Regenerate without sudo.

### Name/IP Mismatch (ERR_CERT_COMMON_NAME_INVALID)
This means the certificate doesn't contain the IP address or domain you are typing. Verify the contents:
openssl x509 -in /etc/httpd/conf/localhost.pem -noout -text | grep -A 1 "Subject Alternative Name"
If the IP is missing, regenerate the certificate with the IP included (see Step 10).

### Connection Timeout
If the browser hangs and times out, your firewall is likely blocking the connection. 
1. Check firewall status: `sudo ufw status`
2. Ensure `443/tcp` is explicitly allowed. 
3. Delete bad rules with: `sudo ufw delete allow 433/tcp`

### Apache Won't Start
Check for errors:
**Arch:** `sudo systemctl status httpd.service`
**Ubuntu:** `sudo systemctl status apache2.service`

Common issues:
- Missing `socache_shmcb` module
- Incorrect certificate file paths in the config file
- Private key permissions are too open (run `chmod 600` on the key file)

## Generating Certificates for Other Local Domains
For custom local domains (e.g., `myproject.local`):
mkcert myproject.local "*.myproject.local"
Then configure Apache virtual hosts accordingly.

## Notes
- Certificates generated by mkcert are valid for 2 years and 3 months.
- The local CA is trusted only on machines where you explicitly install the `rootCA.pem`.
- Never use these certificates on public-facing servers.

---
Created: 2026-09-22
Systems: CachyOS (Arch Linux) & Ubuntu Server
Web Server: Apache / Apache2
Certificate Tool: mkcert
