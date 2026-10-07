Step-by-Step Recap: Enabling Fractal Adjust Devices on CachyOS

Step 1: Open the udev rules file
Open your terminal and use nano to create or edit the file with administrator privileges:
sudo nano /etc/udev/rules.d/50-fractal.rules

Step 2: Add the permission rule
Insert this exact line into the file. It grants read/write access to Fractal USB devices (vendor ID 36bc):
SUBSYSTEMS=="usb*", ATTRS{idVendor}=="36bc", MODE="0666"

Step 3: Save and exit Nano
- Press Ctrl + O (the letter O) to save the file.
- Press Enter to confirm the filename.
- Press Ctrl + X to exit the editor.

Step 4: Apply the new rules immediately
Instead of logging out, tell the system to reload and apply the rules right away:
sudo udevadm control --reload-rules
sudo udevadm trigger

Step 5: Verify the fix
Refresh the Fractal Adjust Pro Web Application. Your headset should now be successfully detected and ready to use.
