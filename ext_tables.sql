CREATE TABLE tx_blogsystem_domain_model_blog (
	title varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL DEFAULT '',
	image int(11) DEFAULT '0' NOT NULL,
	comments int(11) unsigned DEFAULT '0' NOT NULL
);
CREATE TABLE tx_blogsystem_domain_model_comment (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    blog_id int(11) unsigned DEFAULT '0' NOT NULL,
    author_name varchar(255) DEFAULT '' NOT NULL,
    comment_text text NOT NULL,
    approved tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY blog_id (blog_id)
);