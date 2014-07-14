class nginx {
   yumrepo { "nginx":
      baseurl => "http://nginx.org/packages/mainline/centos/7/$basearch/",
      descr => "nginx repo",
      enabled => 1,
      gpgcheck => 0
   }

   package { "nginx": ensure => installed, require => Yumrepo["nginx"] }
}