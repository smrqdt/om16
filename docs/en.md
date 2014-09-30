#order process
* add item [add another item, remove item, clear cart, checkout]
* enter address [continue] (TODO: missing back)
* review order [submit] (TODO: missing back, change address)
	* email is send to customer
* pay order
	* email is send when payment is received
	* email is send when order is shipped
#order statuses (new, payed, shipped, overdue) (TODO: link to issue)
* new -> payed
	* new -> shipped
* new -> overdue
* payed -> shipped
3. item numbering  
item numbers are not meant to manage the number of items you sell of a kind, but to track wich limited item with a given number is send to wich customer. e.g. you are selling tickets for a concert and each ticket has an unique number. if an order is marked as payed, the system adds the next free numbers to the order description. if an order gets lost, you can invalidate the ticket numbers and send out new ones. if you give out numbered items over another channel, you can mark the numbers as taken, so they get not reused by the system.
4. configuration
	* DB
	* payment methods
	* email
	* image placeholder
5. build custom views
	* change config.php
	* which values are available in which views
6. class documentation (doxygen?)
7. architecture documentation

#TODO (issues):
* add an easy way to add static pages
* add slideview for **hot** articles


