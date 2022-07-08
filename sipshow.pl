#!/usr/bin/perl
my $output = qx/sudo asterisk -rx "sip show peers"/;
print $output;
my $filename = '/srv/www/htdocs/vicidial/sipler.txt';
open(FH, '>', $filename) or die $!;
print FH $output;
close(FH);
