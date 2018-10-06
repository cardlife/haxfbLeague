haxfbLeague
===========

HaxFootball's league database wordpress plugin.

This is a very simple php object oriented league management system that can be installed on various wordpress sites.
It's completely customizable for stats, please modify the Stats.php to fit a certain model.
 The Stats schema is in install.php, so it must be modified as well.

A sample implementation can be found at http://www.haxfb.com

I'm making this open source so that various other game sites can easily modify it to fit their needs.

After installing, Add the following pages manually into wordpress with shortTag as content [shortTag]
//i.e. Page X with shortTag Y, would be a new page X with content [Y].
//Note: Page names and content are completely customizable, as long as they contain the shortTag.

Admin: [display_admin]
Match: [display_match]
Leaders: [display_leaders]

//Update week to counter that you want the default to be displayed, or else the max week will be selected.
Schedule: [display_schedule week="WEEK"]

Team Pages: //TEAM_ID must be the id of the team found in the admin page of edit-team.
Team X: [display_team id="TEAM_ID"]
