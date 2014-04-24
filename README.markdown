UNSW Lecture Bash Script
========================

## Dependencies
- php

## Usage
```
./getClasses.php min_size #semester year
```
Results go to stdout, progress goes to stderr.
For example, to get a list of classes with at least 100 people in semester 2 of
2014, and store the output in output.csv, run:
```
./getClasses.php 100 2 2014 > output.csv
```

The script will output a csv table which can be imported into any spreadsheet
software (MS Excel, Google Sheets, Open Office Spreadsheet, etc).

## Files
- getClasses.php: the main script that scrapes UNSW handbook
- match.txt: a large regular expression that recognizes the relevant parts of the
page
- README.markdown: this file
