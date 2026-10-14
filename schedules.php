<?php
Require_once 'config/database.php';
$stream_url = "https://stream.zeno.fm/dzgdmrn0c5zuv";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedules</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="GHRadio1" />
    <link rel="manifest" href="/site.webmanifest" />
</head>
<body>
    <header class="bg-gradient-to-r from-[#096C62] to-[#E9B138] text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="index.php">
                <div href="index.php" class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center mr-3">
                    <i><img class="rounded-full" src="assets/images/GHRadiologo.jpg" alt="logoimage"></i>
                </div>
                </a>
                <span class="text-xl font-bold"><p>GHRadio1</p><p class="ml-2 text-sm">Real Music Power</p></span>
                
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="about.php" class="hover:text-purple-300 transition">About</a>
                <a href="contact.php" class="hover:text-purple-300 transition">Contact</a>
                <!--<a href="#" class="hover:text-purple-300 transition">Schedules</a>-->
            </nav>
            
            <!-- Mobile Menu Button -->
            <button class="md:hidden focus:outline-none" id="menu-toggle">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-gray-800 py-2 px-4" id="mobile-menu">
            <a href="about.php" class="block py-2 hover:text-purple-300 transition">About</a>
            <a href="contact.php" class="block py-2 hover:text-purple-300 transition">Contact</a>
            <!--<a href="#" class="block py-2 hover:text-purple-300 transition">Schedules</a>-->
        </div>
    </header>

    <section id="schedules" class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <h2 class="text-2xl sm:text-3xl font-bold text-center mb-8 text-black bg-gradient-to-r from-gh-red to-gh-gold py-4 px-6 rounded-lg shadow-lg">
            GHRADIO1 PROGRAM SCHEDULE
        </h2>
        
        
        <div class="block md:hidden space-y-4">
            <!-- Sunday -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-600">
                <div class="bg-red-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-bold text-red-800 text-lg">SUNDAY</h3>
                    <svg class="w-5 h-5 text-red-600 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">12AM-6AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HiLIFE AGOOGO</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6AM-9AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HEAVEN'S GATE</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">9AM-12PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">TIE NWOM PAPA NO</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">12PM-5PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">BISA NWOM (Request Show)</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">5PM-8PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GH ENTERTAINMENT REVIEW</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">8PM-9PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">R.O.G.I.M. HOUR</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">9PM-2AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">WEEKEND PARTY MIX</span>
                    </div>
                </div>
            </div>
    
            <!-- Monday -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-blue-600">
                <div class="bg-blue-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-bold text-blue-800 text-lg">MONDAY</h3>
                    <svg class="w-5 h-5 text-blue-600 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">2AM-6AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HiLIFE AGOOGO</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6AM-10AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GYE ME TAATAA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10AM-2PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">WAKE UP GHANA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">1:30PM-3PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GIRLS KASA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">2PM-6PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">DONKOMI</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6PM-10PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HAPPY HOUR</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10PM-2AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">WONIM DWOM (Request Show)</span>
                    </div>
                </div>
            </div>
    
            <!-- Tuesday -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-green-600">
                <div class="bg-green-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-bold text-green-800 text-lg">TUESDAY</h3>
                    <svg class="w-5 h-5 text-green-600 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">2AM - 6AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HiLIFE AGOOGO</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6AM - 10AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GYE ME TAATAA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10AM - 2PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">WAKE UP GHANA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">2PM - 6PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">DONKOMI</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6PM - 10PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HAPPY HOUR</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10PM - 2AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">REAL MUSIC POWER</span>
                    </div>
                </div>
            </div>

            <!--WED-->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-purple-600">
                <div class="bg-purple-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-bold text-purple-800 text-lg">WEDNESDAY</h3>
                    <svg class="w-5 h-5 text-purple-600 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">2AM - 6AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HiLIFE AGOOGO</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6AM - 10AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GYE ME TAATAA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10AM - 2PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">WAKE UP GHANA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">1PM - 3PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GIRLS KASA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">3PM - 6PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">DONKOMI</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6PM - 10PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HAPPY HOUR</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10PM - 2AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">REAL MUSIC POWER</span>
                    </div>
                </div>
            </div>

            <!-- Thursday -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-yellow-600">
                <div class="bg-yellow-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-bold text-yellow-800 text-lg">THURSDAY</h3>
                    <svg class="w-5 h-5 text-yellow-600 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">2AM - 6AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HiLIFE AGOOGO</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6AM - 10AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GYE ME TAATAA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10AM - 2PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">WAKE UP GHANA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">2PM - 6PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">DONKOMI</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6PM - 10PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HAPPY HOUR</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10PM - 2AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">WONIM NWOM (Request Show)</span>
                    </div>
                </div>
            </div>

            <!-- Friday -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-indigo-600">
                <div class="bg-indigo-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-bold text-indigo-800 text-lg">FRIDAY</h3>
                    <svg class="w-5 h-5 text-indigo-600 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">2AM - 6AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HiLIFE AGOOGO</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6AM - 10AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GYE ME TAATAA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10AM - 2PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">WAKE UP GHANA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">1:30PM - 3:00PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">GIRLS KASA (+18)</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">3PM - 6PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">DONKOMI</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6PM - 10PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">HAPPY HOUR</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10PM - 2AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">REAL MUSIC POWER</span>
                    </div>
                </div>
            </div>

            <!-- Saturday -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-orange-600">
                <div class="bg-orange-100 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-bold text-orange-800 text-lg">SATURDAY</h3>
                    <svg class="w-5 h-5 text-orange-600 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">12AM - 6AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">REAL MUSIC POWER</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">6AM - 9AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">NS3NKU NWOM</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">9AM - 12PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">AHOMA NSIA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">12PM - 5PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">TE AHOMA NO (Request Show)</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">5PM - 8PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">DI ASA</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">8PM - 10PM</span>
                        <span class="col-span-2 text-gray-800 font-medium">ABRABO Y3 NTOBOASE3</span>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <span class="col-span-1 text-gray-500 font-medium">10PM - 2AM</span>
                        <span class="col-span-2 text-gray-800 font-medium">REAL MUSIC POWER</span>
                    </div>
                </div>
            </div>    
        </div>
        
        <!-- Desktop View (Table) -->
        <div class="hidden md:block overflow-x-auto rounded-lg shadow-xl">
            <table class="w-full bg-white">
                <thead class="bg-gradient-to-r from-gh-red to-gh-gold text-white">
                    <tr>
                        <th class="py-4 px-6 text-left font-bold w-1/6">DAY</th>
                        <th class="py-4 px-6 text-left font-bold w-1/4">TIME</th>
                        <th class="py-4 px-6 text-left font-bold">PROGRAM</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Sunday -->
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="py-4 px-6 font-bold text-red-700 align-top" rowspan="7">Sunday</td>
                        <td class="py-4 px-6">12:00AM - 6:00AM</td>
                        <td class="py-4 px-6 font-semibold">HiLIFE AGOOGO</td>
                    </tr>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="py-4 px-6">6:00AM - 9:00AM</td>
                        <td class="py-4 px-6 font-semibold">HEAVEN'S GATE</td>
                    </tr>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="py-4 px-6">9:00AM - 12:00PM</td>
                        <td class="py-4 px-6 font-semibold">TIE NWOM PAPA NO</td>
                    </tr>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="py-4 px-6">12:00PM - 5:00PM</td>
                        <td class="py-4 px-6 font-semibold">BISA NWOM (Request Show)</td>
                    </tr>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="py-4 px-6">5:00PM - 8:00PM</td>
                        <td class="py-4 px-6 font-semibold">GH ENTERTAINMENT REVIEW</td>
                    </tr>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="py-4 px-6">8:00PM - 9:00PM</td>
                        <td class="py-4 px-6 font-semibold">R.O.G.I.M. HOUR</td>
                    </tr>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="py-4 px-6">9:00PM - 2:00AM</td>
                        <td class="py-4 px-6 font-semibold">WEEKEND PARTY MIX</td>
                    </tr>
                    
                    <!-- Monday -->
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="py-4 px-6 font-bold text-blue-700 align-top" rowspan="7">Monday</td>
                        <td class="py-4 px-6">2:00AM - 6:00AM</td>
                        <td class="py-4 px-6 font-semibold">HiLIFE AGOOGO</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="py-4 px-6">6:00AM - 10:00AM</td>
                        <td class="py-4 px-6 font-semibold">GYE ME TAATAA</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="py-4 px-6">10:00AM - 2:00PM</td>
                        <td class="py-4 px-6 font-semibold">WAKE UP GHANA</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="py-4 px-6">1:30PM - 3:00PM</td>
                        <td class="py-4 px-6 font-semibold">GIRLS KASA</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="py-4 px-6">2:00PM - 6:00PM</td>
                        <td class="py-4 px-6 font-semibold">DONKOMI</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="py-4 px-6">6:00PM - 10:00PM</td>
                        <td class="py-4 px-6 font-semibold">HAPPY HOUR</td>
                    </tr>
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="py-4 px-6">10:00PM - 2:00AM</td>
                        <td class="py-4 px-6 font-semibold">WONIM DWOM (Request Show)</td>
                    </tr>
                    <!-- Tuesday -->
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="py-4 px-6 font-bold text-green-700 align-top" rowspan="7">Tuesday</td>
                        <td class="py-4 px-6">2:00AM - 6:00AM</td>
                        <td class="py-4 px-6 font-semibold">HiLIFE AGOOGO</td>
                    </tr>
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="py-4 px-6">6:00AM - 10:00AM</td>
                        <td class="py-4 px-6 font-semibold">GYE ME TAATAA</td>
                    </tr>
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="py-4 px-6">10:00AM - 2:00PM</td>
                        <td class="py-4 px-6 font-semibold">WAKE UP GHANA</td>
                    </tr>
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="py-4 px-6">2:00PM - 6:00PM</td>
                        <td class="py-4 px-6 font-semibold">DONKOMI</td>
                    </tr>
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="py-4 px-6">6:00PM - 10:00PM</td>
                        <td class="py-4 px-6 font-semibold">HAPPY HOUR</td>
                    </tr>
                    <tr class="hover:bg-green-50 transition-colors">
                        <td class="py-4 px-6">10:00PM - 2:00AM</td>
                        <td class="py-4 px-6 font-semibold">REAL MUSIC POWER</td>
                    </tr>
                    <!-- Wednesday -->
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="py-4 px-6 font-bold text-purple-700 align-top" rowspan="7">Wednesday</td>
                        <td class="py-4 px-6">2:00AM - 6:00AM</td>
                        <td class="py-4 px-6 font-semibold">HiLIFE AGOOGO</td>
                    </tr>
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="py-4 px-6">6:00AM - 10:00AM</td>
                        <td class="py-4 px-6 font-semibold">GYE ME TAATAA</td>
                    </tr>
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="py-4 px-6">10:00AM - 2:00PM</td>
                        <td class="py-4 px-6 font-semibold">WAKE UP GHANA</td>
                    </tr>
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="py-4 px-6">1:00PM - 3:00PM</td>
                        <td class="py-4 px-6 font-semibold">GIRLS KASA</td>
                    </tr>
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="py-4 px-6">3:00PM - 6:00PM</td>
                        <td class="py-4 px-6 font-semibold">DONKOMI</td>
                    </tr>
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="py-4 px-6">6:00PM - 10:00PM</td>
                        <td class="py-4 px-6 font-semibold">HAPPY HOUR</td>
                    </tr>
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="py-4 px-6">10:00PM - 2:00AM</td>
                        <td class="py-4 px-6 font-semibold">REAL MUSIC POWER</td>
                    </tr>
                    <!-- Thursday -->
                    <tr class="hover:bg-yellow-50 transition-colors">
                        <td class="py-4 px-6 font-bold text-yellow-700 align-top" rowspan="7">Thursday</td>
                        <td class="py-4 px-6">2:00AM - 6:00AM</td>
                        <td class="py-4 px-6 font-semibold">HiLIFE AGOOGO</td>
                    </tr>
                    <tr class="hover:bg-yellow-50 transition-colors">
                        <td class="py-4 px-6">6:00AM - 10:00AM</td>
                        <td class="py-4 px-6 font-semibold">GYE ME TAATAA</td>
                    </tr>
                    <tr class="hover:bg-yellow-50 transition-colors">
                        <td class="py-4 px-6">10:00AM - 2:00PM</td>
                        <td class="py-4 px-6 font-semibold">WAKE UP GHANA</td>
                    </tr>
                    <tr class="hover:bg-yellow-50 transition-colors">
                        <td class="py-4 px-6">2:00PM - 6:00PM</td>
                        <td class="py-4 px-6 font-semibold">DONKOMI</td>
                    </tr>
                    <tr class="hover:bg-yellow-50 transition-colors">
                        <td class="py-4 px-6">6:00PM - 10:00PM</td>
                        <td class="py-4 px-6 font-semibold">HAPPY HOUR</td>
                    </tr>
                    <tr class="hover:bg-yellow-50 transition-colors">
                        <td class="py-4 px-6">10:00PM - 2:00AM</td>
                        <td class="py-4 px-6 font-semibold">WONIM NWOM (Request Show)</td>
                    </tr>
                    <!-- Friday -->
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="py-4 px-6 font-bold text-indigo-700 align-top" rowspan="7">Friday</td>
                        <td class="py-4 px-6">2:00AM - 6:00AM</td>
                        <td class="py-4 px-6 font-semibold">HiLIFE AGOOGO</td>
                    </tr>
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="py-4 px-6">6:00AM - 10:00AM</td>
                        <td class="py-4 px-6 font-semibold">GYE ME TAATAA</td>
                    </tr>
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="py-4 px-6">10:00AM - 2:00PM</td>
                        <td class="py-4 px-6 font-semibold">WAKE UP GHANA</td>
                    </tr>
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="py-4 px-6">1:30PM - 3:00PM</td>
                        <td class="py-4 px-6 font-semibold">GIRLS KASA (+18)</td>
                    </tr>
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="py-4 px-6">3:00PM - 6:00PM</td>
                        <td class="py-4 px-6 font-semibold">DONKOMI</td>
                    </tr>
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="py-4 px-6">6:00PM - 10:00PM</td>
                        <td class="py-4 px-6 font-semibold">HAPPY HOUR</td>
                    </tr>
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="py-4 px-6">10:00PM - 2:00AM</td>
                        <td class="py-4 px-6 font-semibold">REAL MUSIC POWER</td>
                    </tr>
                    <!-- Saturday -->
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="py-4 px-6 font-bold text-orange-700 align-top" rowspan="7">Saturday</td>
                        <td class="py-4 px-6">12:00AM - 6:00AM</td>
                        <td class="py-4 px-6 font-semibold">REAL MUSIC POWER</td>
                    </tr>
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="py-4 px-6">6:00AM - 9:00AM</td>
                        <td class="py-4 px-6 font-semibold">NS3NKU NWOM</td>
                    </tr>
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="py-4 px-6">9:00AM - 12:00PM</td>
                        <td class="py-4 px-6 font-semibold">AHOMA NSIA</td>
                    </tr>
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="py-4 px-6">12:00PM - 5:00PM</td>
                        <td class="py-4 px-6 font-semibold">TE AHOMA NO (Request Show)</td>
                    </tr>
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="py-4 px-6">5:00PM - 8:00PM</td>
                        <td class="py-4 px-6 font-semibold">DI ASA</td>
                    </tr>
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="py-4 px-6">8:00PM - 10:00PM</td>
                        <td class="py-4 px-6 font-semibold">ABRABO Y3 NTOBOASE3</td>   
                    </tr>
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="py-4 px-6">10:00PM - 2:00AM</td>
                        <td class="py-4 px-6 font-semibold">REAL MUSIC POWER</td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </section>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        document.addEventListener('DOMContentLoaded', function() {
        const player = document.getElementById('radioPlayer');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const playIcon = playPauseBtn.querySelector('i');
        
        function updateButton() {
            if (player.paused) {
                playIcon.className = 'fas fa-play text-white';
            } else {
                playIcon.className = 'fas fa-pause text-white';
            }
        }
        
        playPauseBtn.addEventListener('click', function() {
            if (player.paused) {
                player.play();
            } else {
                player.pause();
            }
            updateButton();
        });
    
    player.addEventListener('play', updateButton);
    player.addEventListener('pause', updateButton);
    player.addEventListener('loadstart', updateButton);
    
    updateButton();
});
    </script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>