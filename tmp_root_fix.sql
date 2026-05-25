DELETE FROM mysql.global_priv WHERE User='root' AND Host IN ('localhost','127.0.0.1');
INSERT INTO mysql.global_priv (Host, User, Priv) VALUES
('localhost','root','{"access":18446744073709551615,"plugin":"mysql_native_password","authentication_string":"","auth_or":[{}],"version_id":100432,"password_last_changed":0}'),
('127.0.0.1','root','{"access":18446744073709551615,"plugin":"mysql_native_password","authentication_string":"","auth_or":[{}],"version_id":100432,"password_last_changed":0}');
FLUSH PRIVILEGES;
SELECT Host, User, Priv FROM mysql.global_priv WHERE User='root';
