# TicketSys
A Ticket System

TODO

- Add user accounts and privileges (session or cookies?)
- - Allow users to customize departments, users, etc...
- Add multiple notes with timestamps (instead of just 1 per ticket)
- - This will need a seperate table and schema adjustment.
- Multiple color schemes
- Improve filter search (keywords, wildcards, etc...)
- Improve UI appearance, especially for the ticket edit section.
- Create an installation script (MySQL)
- Pagination
- Maintenance Functions
- - Prune old closed tickets and/or copy them to a seperate archive table.
- - Autodetect and merge duplicate tickets
- Set static values for ticket Status, Department, etc... 
- Overhaul UI: See below.

Overhaul UI:
The use of modals were experimental. While very slick and sexy, I don't know that they support the fluidity of the rest of the UI. Maybe we need to rethink the entire UI? It's clean, but ugly.

- DONE Create and edit tickets
- DONE Search tickets using very basic filtering.
- DONE Close tickets
- DONE Divide code in to seperate files to improve readability
