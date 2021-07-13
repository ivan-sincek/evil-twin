# Evil Twin - Mark VI

Learn how to set up a fake authentication web page on a fake WiFi network.

Read the comments in these two files to get a better understanding on how all of it works:

* [/src/evil-twin/index.php](https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi/src/evil-twin/index.php)
* [/src/evil-twin/MyPortal.php](https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi/src/evil-twin/MyPortal.php)

You can modify and expand this project to your liking. You have everything you need to get you started.

You can easily customize the [CSS](https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi/src/evil-twin/css/main.css) to make it look more like the company you are testing, e.g. change colors, logo, etc.

Tested on WiFi Pineapple NANO with firmware v2.7.0 and modules Evil Portal v3.2 and Cabinet v1.1.

Additional set up and testing was done on Windows 10 Enterprise OS (64-bit) and Kali Linux v2020.3 (64-bit).

Made for educational purposes. I hope it will help!

In this project I also want to show you how to install and use WiFi Pineapple's modules through GUI, for more console attacks check my [WiFi penetration testing cheat sheet](https://github.com/ivan-sincek/wifi-penetration-testing-cheat-sheet).

## Table of Contents

* [How to Set up a WiFi Pineapple](#how-to-set-up-a-wifi-pineapple)
	* [Windows OS](#windows-os)
	* [Kali Linux](#kali-linux)
* [How to Run](#how-to-run)
* [Remote Packet Capture With Kismet](#remote-packet-capture-with-kismet)
* [Crack WPS PIN](#crack-wps-pin)
* [Sniff WiFi Network Traffic](#sniff-wifi-network-traffic)
* [Images](#images)

## How to Set up a WiFi Pineapple

### Windows OS

Follow the instructions below:

1. [Install the Network Driver](https://www.techspot.com/drivers/driver/file/information/17792)

2. [Setup Basics](https://docs.hak5.org/hc/en-us/articles/360010555313-Setup-Basics)

3. [Windows Setup](https://docs.hak5.org/hc/en-us/articles/360010471434-WiFi-Pineapple-NANO-Windows-Setup)

### Kali Linux

Download and run the following script:

```bash
wget https://raw.githubusercontent.com/hak5darren/wp6/master/wp6.sh && mv wp6.sh /usr/bin/wp6 && chmod +x /usr/bin/wp6 && wp6
```

## How to Run

In the WiFi Pineapple's dashboard go to `Modules -> Manage Modules -> Get Modules from Hak5 Community Repositories` and install `Evil Portal` and `Cabinet` modules, preferably to an SD card storage.

Copy all the content from [\\src\\](https://github.com/ivan-sincek/evil-twin/tree/master/mark_vi/src) to the WiFi Pineapple's `/sd/portals/` (preferred) or `/root/portals/` directory:

```fundamental
scp -r evil-twin root@172.16.42.1:/sd/portals/

scp -r evil-twin root@172.16.42.1:/root/portals/
```

In the WiFi Pineapple's dashboard go to `PineAP` and add the desired names to the SSID pool, then, tick everything except `Capture SSIDs to Pool`.

[Optional] Hide the open access point.

In the WiFi Pineapple's dashboard go to `Networking` and connect your WiFi Pineapple to a real working WiFi network in the `WiFi Client Mode` section to tunnel network traffic back and forth from the Internet.

In the WiFi Pineapple's dashboard go to `Modules -> Evil Portal` and activate the `evil-twin` portal, then, start the `Captive Portal`.

In the WiFi Pineapple's dashboard go to `Modules -> Cabinet`, navigate to `/sd/logs/` or `/root/logs/` directory and click "Edit" on the `evil_twin.log` to view the captured credentials.

Download the log file through SSH:

```fundamental
scp root@172.16.42.1:/sd/logs/evil_twin.log ./

scp root@172.16.42.1:/root/logs/evil_twin.log ./
```

---

Use the SingleFile ([Chrome](https://chrome.google.com/webstore/detail/singlefile/mpiodijhokgodhhofbcjdecpffjipkle))([FireFox](https://addons.mozilla.org/hr/firefox/addon/single-file)) browser extension to download a web page as a single HTML file, then, rename the file to `index.php`.

---

Find out more about the [PineAP](https://docs.hak5.org/hc/en-us/articles/360010555253-The-PineAP-Suite).

Find out how to turn up your WiFi Pineapple's signal strength to missassociate clients to the fake WiFi network from my other [project](https://github.com/ivan-sincek/wifi-penetration-testing-cheat-sheet#1-configuration).

Use filtering so you won't go out of your testing scope.

## Remote Packet Capture With Kismet

Search for WiFi networks within your range, as well as fetch the information and MAC addresses of access points.

On your Kali Linux, download some missing files, then, run the Kismet's server:

```fundamental
wget https://raw.githubusercontent.com/kismetwireless/kismet/master/conf/kismet_httpd.conf -O /etc/kismet/kismet_httpd.conf

wget https://raw.githubusercontent.com/kismetwireless/kismet/master/conf/kismet_manuf.txt.gz -O /etc/kismet/kismet_manuf.txt.gz

kismet
```

Connect to your WiFi Pineapple (remote port forwarding) and install the Kismet's remote capturing tool (to an SD card storage):
```bash
ssh root@172.16.42.1 -R 3501:localhost:3501

opkg update && opkg -d sd install kismet-remotecap-hak5
```

After the installation, create the missing symbolic links:

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

Connect the Kismet's remote capturing tool to the Kismet's server:

```fundamental
airmon-ng start wlan0

kismet_cap_linux_wifi --connect localhost:3501 --source wlan0mon
```

On your Kali Linux, navigate to the Kismet's dashboard (`http://localhost:2501`) with your preferred web browser.

## Crack WPS PIN

In the WiFi Pineapple's dashboard go to `Modules -> Manage Modules -> Get Modules from Hak5 Community Repositories` and install `wps` module (to an SD card storage).

On your WiFi Pineapple, install required packages (to the internal storage):

```bash
opkg update && opkg install libpcap
```

In the WiFi Pineapple's dashboard go to `Modules -> wps`, install the required dependencies (to an SD card storage) and start cracking.

## Sniff WiFi Network Traffic

Once you get an access to a WiFi network, start capturing network packets.

In the WiFi Pineapple's dashboard go to `Modules -> Manage Modules -> Get Modules from Hak5 Community Repositories` and install `tcpdump` module (to an SD card storage).

In the WiFi Pineapple's dashboard go to `Modules -> tcpdump`, install the required dependencies (to an SD card storage) and start capturing packets.

You can download the PCAP file from the `History` section.

You can also pipe the `tcpdump` directly into the Wireshark:

```bash
ssh root@172.16.42.1 tcpdump -U -i wlan0mon -w - | wireshark -k -i -
```

On Windows OS you might need to specify a full path to the Wireshark executable.

## Images

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/img/landing_page_pc.jpg" alt="Landing Page (PC)"></p>

<p align="center">Figure 1 - Landing Page (PC)</p>

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/img/landing_page_mobile.jpg" alt="Landing Page (Mobile)"></p>

<p align="center">Figure 2 - Landing Page (Mobile)</p>

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi/img/log.jpg" alt="Log"></p>

<p align="center">Figure 3 - Log</p>
