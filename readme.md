Tour Divide To Do
=================

Created to show my natural programming style to prospective employers

## Why Tour Divide?

I have a whiteboard with a list of ToDos for my Tour Divide Preparation. It makes a nice simple example I can use for an App.

Development Process
===================

## Template

I started by using the Halo template for which I have a license. The template is based on Materialize which I dont know but as I was using the layout as is I did some quick learnign for the changes I wanted (eg to add Modal dialog).

## Layers

* Database
* API
* Front End

* No login is implemented as this was designed purely to show my natural coding style
* Did not clear out the unused components from the template
* Did not properly verify sql injection concerns


## Database

* ToDo - Stores the ToDo info
* Mail - stores a copy of each mail sent through the contact form

**Concerns**
* No user tracking

## API

I used my standard API for jTable. This API allows me to link in any mySQL table as a CRUD environment using the standards of jTable.org. This allows for Rapid Prototyping as minimal coding is required to create a complex API. I have planned to create a similar generic API for standard CRUD actions.

For this example the API was extended to create a toggle action on the todo tabel to switch status from 0 to 1 or 1 to 0

** Files **

* api/TableActions.php - Generic file to manage CRUD actions (not modified for this project but my own coding)
* api/ajax_todo.php - Configuration for the API and the custom Toggle Action
* api/ajax_mail.php - Saves Contact messages and send a mail

**Concerns**

* No Security layer
* SQL Injection is possible - not a concern for a prototyping environment
* No Auditing (the API allows auditing but was not implemented)
* No spam checking

## Front End

Based on the Halo - Material Design Template (https://themeforest.net/item/halo-material-design-mobile-template/14981441). My own images were added. 

Worked on getting layout correct in the index.html and todon.css files. Once layout was correct I built the functionality (todon.js). Once sufficient functionality was added that no more changes to layout and css were required the index.html was copied for the contact and about pages. Additional functionality were added to these pages (mostly static).

It would make more sense to change the application flow to make the Contact and About pages part of the index.html so that pages would not be reloaded, data ajax calls would not be repeated but for this example I stuck to the frameowkr supplied by the template. Over the long term this would enable additional functionality being added - but unlikely in this case. (I have implemented a complete application as a SPA including full administration functionality and lookup data).

**Files Added/Updated**
* index.html
* about.html
* contact.html
* js/todon.js - modified for my own ToDo functions and to call the ToDo API
* Css/todon.css - Minor modification to base CSS for look and feel. This CSS loaded last to override the base classes where relevant.

**Concerns**
* No edit functions
* No security layer - anyone can modify

## Time Taken

1. Database - minimal
2. API - approc 30mins
3. Front End (ToDo page) - approx 3 hours
4. Creating Markdown - 30 mins
5. Contact and About - 45mins

## Demo

Will be loaded to my website.

http://todo.anndeve.co.za

Who am I?
=========

William Cairns (cairnswm)
Software Developer / Long Distance Cyclist

http://www.facebook.com/cairnswm
http://william.cairns.co.za

What is the Tour Divide?
========================

The tour divide is a 4500km mountain bike race through the American Rocky Mountains. I plan to do the race in 2019. My goal time is 28 days to complete the race but I will be away from home for 44 days.