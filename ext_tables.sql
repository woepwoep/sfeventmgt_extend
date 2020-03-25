#
# Extended table structure for table 'tx_sfeventmgt_domain_model_registration'
#
CREATE TABLE tx_sfeventmgt_domain_model_registration (
	bignr varchar(30) DEFAULT '' NOT NULL,
	venvnr varchar(30) DEFAULT '' NOT NULL,
	geboorteplaats varchar(30) DEFAULT '' NOT NULL,
	functie varchar(30) DEFAULT '' NOT NULL,
	factuurnr varchar(9) DEFAULT '' NOT NULL,
	payment_price double(11,2) DEFAULT '0.00' NOT NULL,
);
