SVN_WORK = $(HOME)/subversion-repo/wordpress-updateable/kblog-metadata/trunk
CP=cp


all:
	$(MAKE) -C .. kblog-metadata


publish_to_svn: 
	$(CP) kblog*php license.txt readme.txt $(SVN_WORK)
	$(CP) -rf HumanNameParser $(SVN_WORK)
