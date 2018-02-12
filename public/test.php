<?php

const GOOGLE_MAPS_LINK = 0;
const LOCATION = 1;
const STREET = 2;
const CITY = 3;
const STATE = 4;
const ZIP = 5;
const LATITUDE = 6;
const LONGITUDE = 7;
const REGION = 8;
const WORKOUT_NAME = 9;
const DAY_OF_WEEK = 10;
const START_TIME = 11;
const END_TIME = 12;
const WORKOUT_STYLE = 13;
const TWITTER_HANDLE = 14;
const TWITTER_NAME = 15;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://docs.google.com/spreadsheets/d/1z4LuujrE9P9Wk4q6YNIKVaGBXTPbRAxX-0SkFSmVt18/gviz/tq?tqx=out:csv&sheet=Data');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$csv = curl_exec($ch);
$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $csv));

//print_r($data);

        ?>
        <div class="f3-table">
            <div class="f3-table-row f3-table-headings">
                <div class="f3-table-cell">Location</div>
                <div class="f3-table-cell">Workout Title</div>
                <div class="f3-table-cell">Day of the Week</div>
                <div class="f3-table-cell">Start Time</div>
                <div class="f3-table-cell">End Time</div>
                <div class="f3-table-cell">Workout Style</div>
                <div class="f3-table-cell">AOQ</div>
            </div>
            <div class="f3-table-body">
        <?php
        $index = 0;

        foreach ($data as $row) {
            $index++;
            if ($index == 1) continue;

            ?>
                <div class="f3-table-row">
                    <div class="f3-table-row-container">
                        <div class="f3-table-cell f3-table-cell-location"><?php echo $row[LOCATION] ?></div>
                        <div class="f3-table-cell f3-table-cell-workout"><?php echo $row[WORKOUT_NAME] ?></div>
                        <div class="f3-table-cell f3-table-cell-day"><?php echo $row[DAY_OF_WEEK] ?></div>
                        <div class="f3-table-cell f3-table-cell-start"><?php echo $row[START_TIME] ?></div>
                        <div class="f3-table-cell f3-table-cell-end"><?php echo $row[END_TIME] ?></div>
                        <div class="f3-table-cell f3-table-cell-style"><?php echo $row[WORKOUT_STYLE] ?></div>
                        <div class="f3-table-cell f3-table-cell-twitter"><?php echo $row[TWITTER_NAME] ?></div>
                    </div>
                </div>
            <?php 
        }
        ?>
            </div>
        </div>
        <?php

curl_close($ch)

?>