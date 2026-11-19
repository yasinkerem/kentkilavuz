<?php
require_once 'includes/baslangic.php';

$_SESSION = [];
session_destroy();

session_start();
mesaj_ekle('bilgi', 'Oturumunuz kapatıldı.');
yonlendir('giris.php');
