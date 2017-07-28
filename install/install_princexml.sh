#!/usr/bin/env bash

sudo apt install libgif7 libcam-pdf-perl
wget https://www.princexml.com/download/prince_11.2-1_ubuntu16.04_amd64.deb
sudo dpkg --install prince_11.2-1_ubuntu16.04_amd64.deb
rm prince_11.2-1_ubuntu16.04_amd64.deb