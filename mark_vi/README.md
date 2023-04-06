# Evil Twin - Mark VI

Learn how to set up a fake authentication web page on a fake WiFi network.

Read the comments in these two files to get a better understanding on how all of it works:

* [/src/evil-twin/index.php](https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi/src/evil-twin/index.php)
* [/src/evil-twin/MyPortal.php](https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi/src/evil-twin/MyPortal.php)

You can modify and expand this project to your liking. You have everything you need to get started.

You can easily customize [CSS](https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi/src/evil-twin/css/main.css) to make it look more like the company you are testing, e.g. change colors, logo, etc.

You can also use SingleFile ([Chrome](https://chrome.google.com/webstore/detail/singlefile/mpiodijhokgodhhofbcjdecpffjipkle))([FireFox](https://addons.mozilla.org/hr/firefox/addon/single-file)) browser extension to download a web page as a single HTML file, rename the file to `index.php`, and use it in the same template.

Tested on WiFi Pineapple NANO with the firmware v2.7.0 and modules Evil Portal v3.2 and Cabinet v1.1.

Additional set up and testing was done on Windows 10 Enterprise OS (64-bit) and Kali Linux v2022.2 (64-bit).

Made for educational purposes. I hope it will help!

In this project I also want to show you how to install and use WiFi Pineapple's modules through GUI, for more console attacks check my [WiFi penetration testing cheat sheet](https://github.com/ivan-sincek/wifi-penetration-testing-cheat-sheet).

## Table of Contents

* [How to Set up a WiFi Pineapple](#how-to-set-up-a-wifi-pineapple)
	* [Windows OS](#windows-os)
	* [Kali Linux](#kali-linux)
* [How to Run](#how-to-run)
	* [Spoof All SSIDs](#spoof-all-ssids)
* [Remote Packet Capture With Kismet](#remote-packet-capture-with-kismet)
* [Crack WPS PIN](#crack-wps-pin)
* [Sniff WiFi Network Traffic](#sniff-wifi-network-traffic)
* [Images](#images)

## How to Set Up the WiFi Pineapple

In case you might need it, check [frimware recovery/upgrade](https://downloads.hak5.org/pineapple).

### Windows OS

Follow the instructions below:

1. [Install Network Driver](https://www.techspot.com/drivers/driver/file/information/17792)

2. [Setup Basics](https://docs.hak5.org/wifi-pineapple-6th-gen-nano-tetra/setup/setup-basics)

3. [Windows Setup](https://docs.hak5.org/wifi-pineapple-6th-gen-nano-tetra/setup/wifi-pineapple-nano-windows-setup)

### Kali Linux

Download and run the following script:

```bash
wget https://downloads.hak5.org/api/devices/wifipineapplenano/tools/wp6.sh/1.0/linux -O wp6.sh && mv wp6.sh /usr/bin/wp6 && chmod +x /usr/bin/wp6 && wp6
```

## How to Run

In WiFi Pineapple's dashboard go to `Modules -> Manage Modules -> Get Modules from Hak5 Community Repositories` and install `Evil Portal` and `Cabinet` modules, preferably to an SD card storage.

Copy all the content from [\\src\\](https://github.com/ivan-sincek/evil-twin/tree/master/mark_vi/src) to WiFi Pineapple's `/sd/portals/` (preferred) or `/root/portals/` directory:

```fundamental
scp -r evil-twin root@172.16.42.1:/sd/portals/evil-twin

scp -r evil-twin root@172.16.42.1:/root/portals/evil-twin
```

Go to `Networking`, and set the `Open AP SSID` to your desired (portal) name.

Go to `Filters`, and make sure both client and SSID filter lists are set to deny mode.

In WiFi Pineapple's dashboard go to `Networking` and connect your WiFi Pineapple to a real working WiFi network in `WiFi Client Mode` section to tunnel network traffic back and forth from the Internet.

In WiFi Pineapple's dashboard go to `Modules -> Evil Portal` and activate the portal, then, start `Captive Portal`.

In WiFi Pineapple's dashboard go to `Modules -> Cabinet`, navigate to `/sd/logs/` or `/root/logs/` directory and click "Edit" on `evil_twin.log` to view the captured credentials.

Download the log file through SSH:

```fundamental
scp root@172.16.42.1:/sd/logs/evil_twin.log ./

scp root@172.16.42.1:/root/logs/evil_twin.log ./
```

### Spoof All SSIDs

Hide the open access point.

In WiFi Pineapple's dashboard go to `PineAP` and add desired (portal) names to `SSID Pool`, then, tick all the checkboxes to spoof all the access points in your range.

---

Find out more about PineAP [here](https://docs.hak5.org/wifi-pineapple-6th-gen-nano-tetra/getting-started/the-pineap-suite).

Find out how to turn up your WiFi Pineapple's signal strength to missassociate clients to the fake WiFi network from my other [project](https://github.com/ivan-sincek/wifi-penetration-testing-cheat-sheet#1-configuration).

Use filtering so you won't go out of your testing scope.

## Remote Packet Capture With Kismet

Search for WiFi networks within your range and fetch their MAC address, vendor name, etc.

On your Kali Linux, download some missing files, then, run Kismet's server:

```fundamental
wget https://raw.githubusercontent.com/kismetwireless/kismet/master/conf/kismet_httpd.conf -O /etc/kismet/kismet_httpd.conf

wget https://raw.githubusercontent.com/kismetwireless/kismet/master/conf/kismet_manuf.txt.gz -O /etc/kismet/kismet_manuf.txt.gz

kismet
```

Connect to your WiFi Pineapple (remote port forwarding) and install Kismet's remote capturing tool (to an SD card storage):

```bash
ssh root@172.16.42.1 -R 3501:localhost:3501

opkg update && opkg -d sd install kismet-remotecap-hak5
```

After the installation, create missing symbolic links:

```fundamental
ln -s /sd/usr/lib/libgpg-error.so.0.27.0 /usr/lib/libgpg-error.so.0

ln -s /sd/usr/lib/libgcrypt.so.20.2.5 /usr/lib/libgcrypt.so.20

ln -s /sd/usr/lib/libgnutls.so.30.28.1 /usr/lib/libgnutls.so.30

ln -s /sd/usr/lib/libmicrohttpd.so.12.49.0 /usr/lib/libmicrohttpd.so

ln -s /sd/usr/lib/libmicrohttpd.so.12.49.0 /usr/lib/libmicrohttpd.so.12

ln -s /sd/usr/lib/libcap.so.2 /usr/lib/libcap.so

ln -s /sd/usr/lib/libcap.so.2.27 /usr/lib/libcap.so.2

ln -s /sd/usr/lib/libprotobuf-c.so.1.0.0 /usr/lib/libprotobuf-c.so.1

ln -s /sd/usr/lib/libdw-0.177.so /usr/lib/libdw.so.1
```

Connect Kismet's remote capturing tool to Kismet's server:

```fundamental
airmon-ng start wlan0

kismet_cap_linux_wifi --connect localhost:3501 --source wlan0mon
```

On your Kali Linux, navigate to Kismet's dashboard (`http://localhost:2501`) with your preferred web browser.

## Crack WPS PIN

In WiFi Pineapple's dashboard go to `Modules -> Manage Modules -> Get Modules from Hak5 Community Repositories` and install `wps` module (to an SD card storage).

On your WiFi Pineapple, install required packages (to the internal storage):

```bash
opkg update && opkg install libpcap
```

In WiFi Pineapple's dashboard go to `Modules -> wps`, install the required dependencies (to an SD card storage) and start cracking.

## Sniff WiFi Network Traffic

Once you get an access to a WiFi network, start capturing network packets.

In WiFi Pineapple's dashboard go to `Modules -> Manage Modules -> Get Modules from Hak5 Community Repositories` and install `tcpdump` module (to an SD card storage).

In WiFi Pineapple's dashboard go to `Modules -> tcpdump`, install the required dependencies (to an SD card storage) and start capturing packets.

You can download the PCAP file from `History` section.

You can also pipe `tcpdump` from WiFi Pineapple directly to Wireshark:

```bash
ssh root@172.16.42.1 tcpdump -U -i wlan0mon -w - | wireshark -k -i -
```

On Windows OS you might need to specify a full path to the executable.

## Images

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/img/landing_page_pc.jpg" alt="Landing Page (PC)"></p>

<p align="center">Figure 1 - Landing Page (PC)</p>

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/img/landing_page_mobile.jpg" alt="Landing Page (Mobile)"></p>

<p align="center">Figure 2 - Landing Page (Mobile)</p>

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi/img/log.jpg" alt="Log"></p>

<p align="center">Figure 3 - Log</p>
