using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;
using System.Xml.Linq;
using MySql.Data.Common;
using MySql.Data.MySqlClient;

namespace exam_prep_desktop {
    internal static class Backend {
        private static MySqlConnection connection;

        internal static bool Initialize() {
#warning database connection credentials
            string dbhost = "localhost";
            string dbuser = "root";
            string connection_string = $"server={dbhost};user={dbuser}";
            connection = new MySqlConnection(connection_string);
            try {
                connection.Open();
                // multilanguage code let's go. it's a good idea i swear.
                // for clarification, data modelling & db structure wasn't my job
#warning database name, potentially non-existent
                string dbname = "tetelek";
                MySqlCommand use = new($"use {dbname};", connection);
                use.ExecuteNonQuery();
                return connection.State == System.Data.ConnectionState.Open;
            } catch {
                return false;
            }
        }

        internal static void Cleanup() {
            if(connection.State == System.Data.ConnectionState.Open) {
                connection.Close();
            }
            connection.Dispose();
        }

        internal static List<string> QuerySubjects() {
            List<string> subjects = [];
            // the thing i really don't like about dealing with a database is that i have no way of ensuring
            // the field or table i try accessing really exists other than looking at the database myself.
            // but the compiler has no way to check and therefore it's not safe
            // this is the best i can do to prevent errors
#warning field name in database, potentially non-existent
            string fieldName = "tantargy";
#warning table name in database, potentially non-existent
            string tableName = "tantargyak";
            string query = $"SELECT {fieldName} FROM {tableName};";
            MySqlCommand cmd = new(query, connection);
            MySqlDataReader results = cmd.ExecuteReader();
            while(results.Read()) {
                subjects.Add(results.GetString(results.GetOrdinal(fieldName)));
            }
            results.Close();
            results.Dispose();
            cmd.Dispose();
            return subjects;
        }

        internal static List<Topic> QueryTopics(string subject) {
            List<Topic> topics = [];

            // i really hate to hardcode these, but i'll just generate a warning again and roll with it
            // a dictionary might be slightly better, but i just want to get this over with so i can
            // actually study
#warning hardcoded names in db
            string query = $"SELECT tetelcimek.id, cim, vazlat, kidolgozas, modosit, tantargy FROM " +
                $"tetelcimek LEFT JOIN tantargyak ON tetelcimek.tantargyid = tantargyak.id WHERE " +
                // if the subjects weren't hardcoded, and the user had any kind of control over it,
                // this variable should be sanitized, but it's fine in this case
                $"tantargy = '{subject}';";
            MySqlCommand cmd = new(query, connection);
            MySqlDataReader results = cmd.ExecuteReader();
            while(results.Read()) {
                topics.Add(new Topic {
                    id = results.GetInt32(results.GetOrdinal("id")),
                    title = results.GetString(results.GetOrdinal("cim")),
                    draft = results.GetString(results.GetOrdinal("vazlat")),
                    final = results.GetString(results.GetOrdinal("kidolgozas")),
                    lastModified = results.GetDateTime(results.GetOrdinal("modosit")),
                    subject = results.GetString(results.GetOrdinal("tantargy")),
                });
            }
            results.Close();
            results.Dispose();
            cmd.Dispose();

            return topics;
        }

        // slight copy-paste but it's my birthday for crying out lout and i wanna do something
        // actually enjoyable like playing the guitar instead of dealing with microsoft's idiocy
        // at least on this one day
        internal static List<Topic> SearchTopics(string searchTerm) {
            List<Topic> topics = [];

#warning hardcoded names in db
            string query = $"SELECT tetelcimek.id, cim, vazlat, kidolgozas, modosit, tantargy FROM " +
                $"tetelcimek LEFT JOIN tantargyak ON tetelcimek.tantargyid = tantargyak.id WHERE " +
                // no sql injection on my watch
                // unless this is the deprecated, faulty mysql_escape_string, and not the patched
                // mysql_real_escape_string because nowhere in the docs does it say anything about that
                $"cim LIKE \"%{MySqlHelper.EscapeString(searchTerm)}%\" OR " +
                $"vazlat LIKE \"%{MySqlHelper.EscapeString(searchTerm)}%\" OR " +
                $"kidolgozas LIKE \"%{MySqlHelper.EscapeString(searchTerm)}%\";";
            MySqlCommand cmd = new(query, connection);
            MySqlDataReader results = cmd.ExecuteReader();
            while(results.Read()) {
                topics.Add(new Topic {
                    id = results.GetInt32(results.GetOrdinal("id")),
                    title = results.GetString(results.GetOrdinal("cim")),
                    draft = results.GetString(results.GetOrdinal("vazlat")),
                    final = results.GetString(results.GetOrdinal("kidolgozas")),
                    lastModified = results.GetDateTime(results.GetOrdinal("modosit")),
                    subject = results.GetString(results.GetOrdinal("tantargy")),
                });
            }
            results.Close();
            results.Dispose();
            cmd.Dispose();

            return topics;
        }

        internal static void AddTopic(Topic topic) {
            int? subjectid = GetSubjectId(topic.subject);
            if(subjectid.HasValue) {
#warning hardcoded db names
                string query = $"INSERT INTO tetelcimek " +
                    $"(tetelcimek.cim, tetelcimek.vazlat, tetelcimek.kidolgozas, tetelcimek.tantargyid, tetelcimek.modosit) " +
                    $"VALUES (\"{MySqlHelper.EscapeString(topic.title)}\", " +
                    $"\"{MySqlHelper.EscapeString(topic.draft)}\", " +
                    $"\"{MySqlHelper.EscapeString(topic.final)}\", " +
                    $"\"{MySqlHelper.EscapeString(subjectid.Value.ToString())}\", " +
                    $"\"{DateTime.Now:yyyy-MM-dd}\");";
                MySqlCommand cmd = new(query, connection);
                cmd.ExecuteNonQuery();
                cmd.Dispose();
            } else {
                MessageBox.Show("Unknown subject", "Critical error");
                Application.Exit();
            }
        }

        internal static void EditTopic(Topic topic) {
            int? subjectid = GetSubjectId(topic.subject);
            if(subjectid.HasValue) {
#warning hardcoded db names
                string query = $"UPDATE tetelcimek INNER JOIN tantargyak ON tetelcimek.tantargyid = tantargyak.id " +
                    $"SET tetelcimek.cim = \"{MySqlHelper.EscapeString(topic.title)}\", " +
                    $"tetelcimek.vazlat = \"{MySqlHelper.EscapeString(topic.draft)}\", " +
                    $"tetelcimek.kidolgozas = \"{MySqlHelper.EscapeString(topic.final)}\", " +
                    $"tetelcimek.tantargyid = \"{MySqlHelper.EscapeString(subjectid.Value.ToString())}\", " +
                    $"tetelcimek.modosit = \"{DateTime.Now:yyyy-MM-dd}\" " +
                    $"WHERE tetelcimek.id = {topic.id};";
                MySqlCommand cmd = new(query, connection);
                cmd.ExecuteNonQuery();
                cmd.Dispose();
            } else {
                MessageBox.Show("Unknown subject", "Critical error");
                Application.Exit();
            }
        }

        internal static int? GetSubjectId(Subject subject) {
            int? id = null;

#warning hardcoded db names
            string query = $"SELECT id FROM tantargyak WHERE tantargyak.tantargy = \"{MySqlHelper.EscapeString(subject)}\" LIMIT 1;";
            MySqlCommand cmd = new(query, connection);
            MySqlDataReader results = cmd.ExecuteReader();
            while(results.Read()) {
                id = results.GetInt32(results.GetOrdinal("id"));
            }
            results.Close();
            results.Dispose();
            cmd.Dispose();

            return id;
        }
    }
}
