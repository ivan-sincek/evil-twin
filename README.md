# Evil Twin - Mark VII

Learn how to set up a fake authentication web page on a fake WiFi network.

Read the comments in these two files to get a better understanding on how all of it works:

* [/src/evil-twin/index.php](https://github.com/ivan-sincek/evil-twin/blob/master/src/evil-twin/index.php)
* [/src/evil-twin/MyPortal.php](https://github.com/ivan-sincek/evil-twin/blob/master/src/evil-twin/MyPortal.php)

You can modify and expand this project to your liking. You have everything you need to get you started.

You can easily customize [CSS](https://github.com/ivan-sincek/evil-twin/blob/master/src/evil-twin/css/main.css) to make it look more like the company you are testing, e.g. change colors, logo, etc.

Tested on WiFi Pineapple Mark VII Basic with firmware v1.0.2 and modules Evil Portal v1.1 and Cabinet v1.0.

Additional set up and testing was done on Windows 10 Enterprise OS (64-bit) and Kali Linux v2020.3 (64-bit).

Made for educational purposes. I hope it will help!

In this project I also want to show you how to install and use WiFi Pineapple's modules through GUI, for more console attacks check my [WiFi penetration testing cheat sheet](https://github.com/ivan-sincek/wifi-penetration-testing-cheat-sheet).

If you have an older device, check the [WiFi Pineapple Nano \(Mark VI\)](https://github.com/ivan-sincek/evil-twin/blob/master/mark_vi).

## Table of Contents

* [How to Set up a WiFi Pineapple](#how-to-set-up-a-wifi-pineapple)
	* [Windows OS](#windows-os)
	* [Kali Linux](#kali-linux)
* [How to Run](#how-to-run)
* [Kismet](#kismet)
	* [Remote Packet Capture](#remote-packet-capture)
	* [Local Packet Capture](#local-packet-capture)
* [Sniff WiFi Network Traffic](#sniff-wifi-network-traffic)
* [Images](#images)

## How to Set up a WiFi Pineapple

### Windows OS

Follow the instructions below:

1. [Setup Basics](https://docs.hak5.org/hc/en-us/articles/360053346334-Setup-Basics)

2. [Windows Setup](https://docs.hak5.org/hc/en-us/articles/360058458313-Configuring-ICS-on-Windows)

### Kali Linux

Download and run the following script:

```bash
wget https://downloads.hak5.org/wp7.sh && mv wp7.sh /usr/bin/wp7 && chmod +x /usr/bin/wp7 && wp7
```

## How to Run

In the WiFi Pineapple's dashboard go to `Modules -> Manage -> Get Available Modules`, install `Evil Portal` and `Cabinet` modules, and pin them to the sidebar.

Copy all the content from [\\src\\](https://github.com/ivan-sincek/evil-twin/tree/master/src) to the WiFi Pineapple's `/root/portals/` directory:

```fundamental
scp -r evil-twin root@172.16.42.1:/root/portals/
```

In the WiFi Pineapple's dashboard go to `PineAP Suite` and add the desired names to the SSID pool, then, set your settings as in picture below.

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/img/settings.jpg" alt="PineAP Settings"></p>

<p align="center">Figure 1 - PineAP Settings</p>

[Optional] Hide the open access point.

Connect your WiFi Pineapple to a real working WiFi network in the `Settings -> Networking -> WiFi Client Mode` section to tunnel network traffic back and forth from the Internet.

In the WiFi Pineapple's dashboard go to `Evil Portal` and activate the `Evil-Twin` portal, then, click on both `Start Web Server` and `Start`.

In the WiFi Pineapple's dashboard go to `Cabinet`, navigate to `/root/logs/` directory and click "Edit" on the `evil_twin.log` to view the captured credentials.

Download the log file through SSH:

```fundamental
scp root@172.16.42.1:/root/logs/evil_twin.log ./
```

---

Use the SingleFile ([Chrome](https://chrome.google.com/webstore/detail/singlefile/mpiodijhokgodhhofbcjdecpffjipkle))([FireFox](https://addons.mozilla.org/hr/firefox/addon/single-file)) browser extension to download a web page as a single HTML file, then, rename the file to `index.php`.

---

Find out more about the [PineAP Suite](https://docs.hak5.org/hc/en-us/articles/360010555253-The-PineAP-Suite).

Find out how to turn up your WiFi Pineapple's signal strength to missassociate clients to the fake WiFi network from my other [project](https://github.com/ivan-sincek/wifi-penetration-testing-cheat-sheet#1-configuration).

Use filtering so you won't go out of your testing scope.

## Kismet

Search for WiFi networks within your range and fetch their MAC address, vendor's name, etc.

### Remote Packet Capture

On your Kali Linux, download some missing files, then, run the Kismet's server:

```fundamental
wget https://raw.githubusercontent.com/kismetwireless/kismet/master/conf/kismet_httpd.conf -O /etc/kismet/kismet_httpd.conf

wget https://raw.githubusercontent.com/kismetwireless/kismet/master/conf/kismet_manuf.txt.gz -O /etc/kismet/kismet_manuf.txt.gz

kismet
```

Connect to your WiFi Pineapple (remote port forwarding) and install the Kismet's remote capturing tool:
```bash
ssh root@172.16.42.1 -R 3501:localhost:3501

opkg update && opkg install kismet-capture-linux-wifi
```

Connect the Kismet's remote capturing tool to the Kismet's server:

```fundamental
airmon-ng start wlan0

kismet_cap_linux_wifi --tcp --connect localhost:3501 --source wlan0mon
```

On your Kali Linux, navigate to the Kismet's dashboard (`http://localhost:2501`) with your preferred web browser.

### Local Packet Capture

Connect to your WiFi Pineapple, then, install, download some missing files, and run the Kismet's server:

```bash
ssh root@172.16.42.1

opkg update && opkg install kismet

wget https://raw.githubusercontent.com/kismetwireless/kismet/master/conf/kismet_httpd.conf -O /etc/kismet/kismet_httpd.conf

wget https://raw.githubusercontent.com/kismetwireless/kismet/master/conf/kismet_manuf.txt.gz -O /etc/kismet/kismet_manuf.txt.gz

airmon-ng start wlan0

kismet -c wlan0mon
```

On your Kali Linux, navigate to the Kismet's dashboard (`http://172.16.42.1:2501`) with your preferred web browser.

## Sniff WiFi Network Traffic

Once you get an access to a WiFi network, start capturing network packets.

In the WiFi Pineapple's dashboard go to `Modules -> Manage -> Get Available Modules`, install `TCPDump` module, and pin it to the sidebar.

In the WiFi Pineapple's dashboard go to `TCPDump` and start capturing packets.

You can download the PCAP file by clicking on the cloud icon.

You can also pipe the `tcpdump` directly into the Wireshark:

```bash
ssh root@172.16.42.1 tcpdump -U -i wlan0mon -w - | wireshark -k -i -
```

On Windows OS you might need to specify a full path to the Wireshark executable.

## Images

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/img/landing_page_pc.jpg" alt="Landing Page (PC)"></p>

<p align="center">Figure 2 - Landing Page (PC)</p>

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/img/landing_page_mobile.jpg" alt="Landing Page (Mobile)"></p>

<p align="center">Figure 3 - Landing Page (Mobile)</p>

<p align="center"><img src="https://github.com/ivan-sincek/evil-twin/blob/master/img/log.jpg" alt="Log"></p>

<p align="center">Figure 4 - Log</p>
