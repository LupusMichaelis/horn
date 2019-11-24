Horn framework purpose is to increase my skills in PHP developpement, and
propose a plain set of ready-to-use standard applications.

This developpement try to stick the DRY principle, and keep coupling to a
minimum level. The MVC design pattern is used to do so. Keep in mind that MVC
design pattern is purpose to interpretation. So you can be surprised at some
meaning of my interpretation (like the SGBDR treat as a view like others). An
other point is the test driven way choose to assure reliability of the Horn
framework.

My main concern in developping is security, followed by robustness then
performance and scalability.

Files organisation :

* `config.php`	A temporary file for storing conficurations.
* `horn/lib`	The framework definition sources.
* `horn/tests`	Unit tests.
* `horn/apps`	A set of standard applications.
* `horn/public`	Some deployment applications examples.
