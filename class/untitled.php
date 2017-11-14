<?php

$format_number = '';


                                        $numero = 1234.5678;

                                        if(is_numeric($numero)) {
                                            $format_number = number_format($numero, 2);
                                        } else {
                                            $format_number = "No disponible";
                                        }

                                        echo "hola: " . $format_number;
                             ?>