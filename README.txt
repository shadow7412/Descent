Firstly:
	All art, character names, dungeon names, procedures, terminology is property of fantasyflightgames.
	This tool is not meant or designed to replace the game, nor any part of it, but just to make keeping a notebook a little bit easier.
	To use this tool you must own the board game "Descent - Sea of Blood".
	The source code however - is open source.

Anyway, with that over with:

Purpose:
Replace the notebook with a web application that both heroes and overlords have open.
It must:
- Be quick - we want to play the game rather than stare at a computer waiting for it do work
	- Autoupdate to stay in sync with each other.
	- Be easier than the notebook
	- Be backdateable (allow undoing of actions)
	- Not replace anything BUT the notepad. (For example, we don't care how many treasure maps we have - we have tokens for that)
	- Be nice on a tablet (touchscreen)

Coding Conventions
	For javascript ids:
	[h|o][o|i|v][subject]
	Meaning:
		Hero|Overlord view
		Overworld|Instance|overView
		Whatever is being populated (hero names? whatever...)
	Class 'todo' is used for any object that requires an onclick event but has not yet been assigned one.

References:
	http://www.descentinthedark.com/_h_/heroes.php <-every single hero sheet
	http://stackoverflow.com/questions/3764709/web-based-push-notifications-for-internal-only-application <-this would be interesting to implement

Bugs/Issues
- If the state gets corrupt, it isn't recoverable. (Hasn't happened, shouldn't happen, but just throwing it out there)
	- We could use the log to recreate the game state, just step through it all and 'replay' the game.
- After creating new campaign, it does not come up on mainscreen in that same session
	- JS will have to create a table. It will also haveto update tier of played campaign

Installation:
	Pull to www folder
	Run config/descent.sql
	Set database login - config/settings.php

TODO:
	Views:
		- Overlord - overworld view
		- Hero - overworld view
		- Overlord - instance view
		- Hero - instance view
		- Overview - shows state of full game
		- Advanced - has datatable with full log, is editable.
	Track hero XP spent
	Images images images - there is a lot of fancy descent art that should be used.
	Track home port - for in dungeon heals?

And the rest of this readme is me jotting down random brainstory stuff	
! = todo.
- = done
? = done but needs checking (ie, is the conquest value correct?)

Instance events
	! Player death
	! Glyph activation
	? Master death
	? Boss death
	? Final Boss death
	! Chest looting
	! Level in dungeon
	! Barrel looting
	! Shop purchase

Overworld events
	! Time passes
	! Items purchased (from shop or applicable tier)
	! Train/rumour/alchemy etc
	? Discover location
	? Enter location (to instance mode)
	? Enter encounter

Each Hero
	- XP
	- Level
	- Curses
	- Powerdie (and ability to upgrade)
	- Purchase Skill
	- Special Training

OverLord - do we want/need to track these?
	! Creature Tiers
	! Treachery
Tiers
	! Weeks Passed
	- Bronze (200) Silver (400) Gold (600) Final Battle
	! Divine Favor (on player death)	