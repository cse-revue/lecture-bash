#!/usr/bin/php
<?php

if (count($argv) != 4) {
    file_put_contents('php://stderr', sprintf("Usage: %s min_size #semester year
Results go to stdout, progress goes to stderr.
For example, to get a list of classes with at least 100 people in semester 2 of 2014, and store the output in output.csv, run:
%s 100 2 2014 > output.csv\n", $argv[0], $argv[0]));
    exit;
}

$minEnrols = (int) $argv[1];
if ($argv[2] == "1") {
    $teachingPeriod = "T1 - Teaching Period One";
} else if ($argv[2] == "2") {
    $teachingPeriod = "T2 - Teaching Period Two";
}


ob_start();
require("match.txt");
$raw = ob_get_clean();
$courseRegex = preg_replace('/ [\n\r\t]+[ ]+/', '[ \n\r\t]+', $raw);
$courseRegex = preg_replace('/ \n/', '', $courseRegex);

//print $courseRegex;

$listStub = "http://www.handbook.unsw.edu.au/vbook$argv[3]/brCoursesByAtoZ.jsp?StudyLevel=Undergraduate&descr=";
$timeTableStub = "http://www.timetable.unsw.edu.au/current/";

$count = 0;

printf("%s; %s; %s; %s; %s; %s; %s\n", "Code", "Course Name", "Enrols", "Day", "Time", "Sec", "Location");


for ($letter =  "A" ; $letter <= "Z" ; $letter++) {
    if ($letter == "AA") {
        break;
    }

    $letterPage = file_get_contents($listStub . $letter);
    
    file_put_contents('php://stderr', "Searching $letter...");

    preg_match_all('/" align="left">([A-Z]{4}[0-9]{4})<\/TD>/', $letterPage, $matches);
    
    $c = 0;
    for ($i = 1; $i < count($matches); $i++) {
        foreach($matches[$i] as $a) {
            $c++;
        }
    }
    file_put_contents('php://stderr', "$c\n");

    $d = 1;

    for ($i = 1 ; $i < count($matches) ; $i++) {
        foreach($matches[$i] as $j) {
            
            file_put_contents('php://stderr', "($d/$c) $j");
            $d++;

            $timeTablePage = @file_get_contents($timeTableStub . $j . ".html");
            
            if ($timeTablePage) {
                $courseName = "";
                if (preg_match('/>' . $j . ' ([A-Za-z0-9\-\.\/:,\(\)&; ]+)</', $timeTablePage, $courseNameMatch)) {
                    $courseName = preg_replace('/&amp;/', '&', $courseNameMatch[1]);
                }
                
                preg_match_all('/'.$courseRegex.'/', $timeTablePage, $courseMatches);

                
                $enrols = $courseMatches[3];

                for ( $k = 0 ; $k < count($enrols) ; $k++ ) {
                    if ($enrols[$k] >= $minEnrols) {
                        $lecture[$count]["courseCode"] = $j;
                        $lecture[$count]["courseName"] = $courseName;
                        $lecture[$count]["enrols"] = $enrols[$k];
                        $lecture[$count]["day"] = $courseMatches[4][$k];
                        $lecture[$count]["time"] = $courseMatches[5][$k];
                        $lecture[$count]["location"] = $courseMatches[6][$k];
                        $lecture[$count]["section"] = $courseMatches[1][$k];
                        $lecture[$count]["period"] = $courseMatches[2][$k];
                        
                        if ($lecture[$count]["period"] == $teachingPeriod) {
                        
                            file_put_contents('php://stderr', " x");

                            printf("%s; %s; %s; %s; %s; %s; %s\n", $lecture[$count]["courseCode"],
                                                   $lecture[$count]["courseName"],
                                                   $lecture[$count]["enrols"],
                                                   $lecture[$count]["day"],
                                                   $lecture[$count]["time"],
                                                   $lecture[$count]["section"],
                                                   $lecture[$count]["location"]);
                        }

                        $count++;                       
                        

                    }
                }



            }

            file_put_contents('php://stderr', "\n");
        }
    }

}

//$letterPage = file_get_contents($listStub . "A");
//print $letterPage;


//for ($i = 'A' ; $i <= 'Z' ; $i++) {

    

//}

?>
