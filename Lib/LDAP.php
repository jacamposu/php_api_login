<?php

function f_conectarDirectorioActivo($login, $clave)
{
    $dn = 'OU=People,OU=Users,OU=TKT,OU=CO,OU=Countries,DC=lanet,DC=accorservices,DC=net';
    //  Hostname
    $ds = 'Ldaps://';
    //  Puerto
    $puertoldap = 636;
    //  Usuario
    $ldaprdn = 'LANET\\' . $login;
    //  Constrasena
    $ldappass = $clave;

    $ldapconn = ldap_connect($ds, $puertoldap) or die('Sin conexional LDAP');
    
    if ($ldapconn) {
        $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
        if ($ldapbind) {
            //            $filter = "(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
            /**
             * Freddie 2014/03/21 15:42 Se filtra por el nombre de usuario
             */
            $filter = "(&(objectClass=user)(objectCategory=person)(samaccountname=$login)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
            $fields = array("samaccountname", "mail");
            $sr = ldap_search($ldapconn, $dn, $filter, $fields);
            $info = ldap_get_entries($ldapconn, $sr);            
            //            preprint($info);
            if ($info["count"] == 1) {
                ldap_close($ldapconn);
                return true;
            }
        }
        ldap_close($ldapconn);
    }
    return false;
}