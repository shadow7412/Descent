Purpose:
Replace the notebook with a web application that both heroes and overlords have open.
It must:
- Autoupdate to stay in sync with each other.
- Be easier than the notebook
- Be backdateable (allow undoing of actions)
- Be quick - we want to play the game rather than stare at a computer waiting for it do work
- Not replace anything BUT the notepad. (For example, we don't care how many treasure maps we have - we have tokens for that)

Bugs/Issues
- If the state gets corrupt, it isn't recoverable. (Hasn't happened, but just throwing it out there)
  - We could use the log to recreate the game state

Installation:
	Pull to www folder
	Run config/descent.sql
	Set database login - config/settings.php

TODO:
	Overlord - overworld view
	Hero - overworld view
	Overlord - instance view
	Hero - instance view
	Track XP spent
	Images images images - there is a lot of fancy descent art that should be used.
	Log view (and altering the log)

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
      ! Items purchased (from shop or applicable tier
	  ! Train/Alchemy/etc
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