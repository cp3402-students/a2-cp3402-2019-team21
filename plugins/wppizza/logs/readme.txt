the constant 'WPPIZZA_PATH_LOGS' will always point to this directory (including trailing slash)
so other wppizza plugins could simply write their appropriate log files here 
using that constant (though the log files name should be unique) instead of 
needing their own directory to store them so any wppizza related logs of all 
wppizza type plugins can always be found here

(just for convenience and a suggestion. plugins can of course store their log files wherever they want)