Long tail
=========

SQL query on view taxa-references in database "bionames"


select count(taxonID) as c, issn from `taxa-references` where issn is not null group by issn order by c desc;

