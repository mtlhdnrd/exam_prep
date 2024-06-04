using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace exam_prep_desktop {
    public partial class MainForm : Form {
        public MainForm() {
            InitializeComponent();
            if(!Backend.Initialize()) {
                MessageBox.Show("Failed to connect to database server.", "Critical error");
                // for some reason this does not exit the application, despite the name
                // so let's just be able to connect to db and move on
                Application.Exit();
            }
        }

        private void MainForm_Load(object sender, EventArgs e) {
            tabControl.Size = ClientSize;
            List<Subject> subjects = Backend.QuerySubjects();
            foreach(string subject in subjects) {
                TabPage page = new() {
                    Name = subject,
                    Text = subject,
                    BackColor = Color.White,
                    AutoScroll = true,
                };
                Button add = new() {
                    Name = "addTopic",
                    Text = "Új tétel felvétele",
                    Location = new Point(15, 2),
                    // small aenurysm here. apparently if you don't specify an anchor on one of the axes,
                    // 0 on that axis sets zero to be in the middle of the ClientRectangle. fun times.
                    Anchor = AnchorStyles.Left | AnchorStyles.Top,
                };
                Button search = new() {
                    Text = "Keresés",
                    AutoSize = true,
                    // small aenurysm here. apparently if you don't specify an anchor on one of the axes,
                    // 0 on that axis sets zero to be in the middle of the ClientRectangle. fun times.
                    Anchor = AnchorStyles.Right | AnchorStyles.Top,
                };
                TextBox searchBar = new() {
                    Width = 150,
                    Anchor = AnchorStyles.Right | AnchorStyles.Top,
                };

                //add.Width = page.ClientSize.Width - search.Width - searchBar.Width - 35;
                // look, i tried making this responsive, but it didn't like the searchbar
                add.Width = 150;
                add.Click += new EventHandler((_sender, _e) => { OpenAddWindow(); });

                search.Location = new Point(page.ClientSize.Width - search.Width - 15, 2);
                search.Click += new EventHandler((_sender, _e) => { OpenSearchWindow(searchBar.Text); });

                searchBar.Location = new Point(search.Left - searchBar.Width - 2, 4);

                page.Controls.Add(add);
                page.Controls.Add(search);
                page.Controls.Add(searchBar);
                tabControl.TabPages.Add(page);
            }

            // really janky, and since .net is lazy and rarely renders, this event isn't necessarily fired
            // at the right time. sometimes, the resize happens, event is fired, and then after rendering
            // does it realize it doesn't fit, and puts the horizontal scroll bar at the bottom
            // it would probably work if i could pass the variable as a reference when initially assigning
            // the size of the panel, but c# only allows it to be passed by value, so i can't really tie
            // them together
            foreach(TabPage tabPage in tabControl.TabPages) {
                tabPage.ClientSizeChanged += new EventHandler((_sender, _e) => {
                    foreach(Panel panel in tabPage.Controls.OfType<Panel>()) {
                        panel.Width = tabPage.ClientSize.Width;
                    }
                });
            }
            foreach(TabPage tabPage in tabControl.TabPages) {
                tabPage.ClientSize = tabPage.ClientSize;
            }
            FormClosing += new FormClosingEventHandler((_sender, _e) => Backend.Cleanup());

            LoadTopics(subjects);
        }

        private void LoadTopics(List<Subject> subjects) {
            foreach(Subject subject in subjects) {
                // should be sanitized, but the subjects are hardcoded so it's not that big of an issue
                List<Topic> topics = Backend.QueryTopics(subject);
                TabPage currentTabPage = tabControl.TabPages[tabControl.TabPages.IndexOfKey(subject)];
                GenerateTopicCards(
                    currentTabPage,
                    topics,
                    currentTabPage.Controls[currentTabPage.Controls.IndexOfKey("addTopic")].Height + 5
                );
            }
        }

        private void GenerateTopicCards(Control parent, List<Topic> topics, int paddingTop) {
            foreach(Topic topic in topics) {
                Panel panel = new() {
                    Width = parent.ClientSize.Width,
                    BorderStyle = BorderStyle.FixedSingle,
                    // yes, these are random numbers i just made up and hardcoded. yes, i know this is bad.
                    // i also know that this works. and if you resize it to an unreasonable size, that's on you.
                    Height = 50,
                    Location = new Point(0, topics.IndexOf(topic) * 52 + paddingTop),
                };
                Label title = new() {
                    Text = topic.title,
                    AutoSize = true,
                };
                title.Location = new Point(15, (panel.ClientSize.Height / 2) - title.Font.Height / 2);
                Button edit = new() {
                    Text = "Módosítás",
                    Anchor = AnchorStyles.Right,
                };
                edit.Location = new Point(panel.Width - edit.Width - 15, (panel.ClientSize.Height / 2) - edit.Height / 2);
                edit.Click += new EventHandler((_sender, _e) => { OpenEditWindow(topic); });
                // oh hell no, if the label covers the panel (which it has to to be visible), then the panel's
                // event isn't fired, even though the label doesn't have an eventhandler for its click event
                panel.Click += new EventHandler((_sender, _e) => { OpenTopicViewWindow(topic); });
                // i love this just as much as i love stepping in broken glass or a rusty nail
                title.Click += new EventHandler((_sender, _e) => { OpenTopicViewWindow(topic); });

                panel.Controls.Add(title);
                panel.Controls.Add(edit);
                parent.Controls.Add(panel);
            }
        }

        // full copy-paste again, this needs improvement. but not bad for a demo or alpha release
        private void OpenAddWindow() {
            Form addForm = new() {
                Text = "Új tétel felvétele",
                StartPosition = FormStartPosition.CenterScreen,
                Size = Size,
                FormBorderStyle = FormBorderStyle.FixedSingle,
                MinimizeBox = false,
                MaximizeBox = false,
            };

            Label titleLabel = new() {
                Text = "Cím",
                Location = new Point(15, 15),
            };
            TextBox title = new() {
                Width = 150,
                Location = new Point(15, titleLabel.Bottom),
            };

            Label draftLabel = new() {
                Text = "Vázlat",
                Location = new Point(15, title.Bottom + 15),
            };
            TextBox draft = new() {
                Width = addForm.ClientSize.Width / 2 - 30,
                Location = new Point(15, draftLabel.Bottom),
                Multiline = true,
            };

            Label finalLabel = new() {
                Text = "Kidolgozás",
                Location = new Point(draft.Right + 15, title.Bottom + 15),
            };
            TextBox final = new() {
                Width = addForm.ClientSize.Width / 2 - 30,
                Location = new Point(draft.Right + 15, finalLabel.Bottom),
                Multiline = true,
                Enabled = draft.Text != "",
            };

            Label subjectLabel = new() {
                Text = "Tantárgy",
                Location = new Point(draft.Right + 15, 15),
            };
            ComboBox subject = new() {
                Width = 150,
                Location = new Point(draft.Right + 15, titleLabel.Bottom),
                DropDownStyle = ComboBoxStyle.DropDownList,
            };
            foreach(Subject availableSubject in Backend.QuerySubjects()) {
                subject.Items.Add(availableSubject);
            }

            Button cancel = new() {
                Text = "Elvetés",
            };

            Button save = new() {
                Text = "Feltöltés",
            };

            draft.Height = addForm.ClientSize.Height - draft.Bottom - 15 - save.Height;
            // hotfix to not allow final text with an empty draft (as easily, still doable but this is fine
            // for a rudimentary hotfix in alpha or a demo)
            draft.TextChanged += new EventHandler((_sender, _e) => { final.Enabled = draft.Text != ""; });
            final.Height = addForm.ClientSize.Height - final.Bottom - 15 - save.Height;

            cancel.Location = new Point(
                addForm.ClientRectangle.Right - cancel.Width - 15,
                addForm.ClientRectangle.Bottom - cancel.Height - 15
            );
            cancel.Anchor = AnchorStyles.Right | AnchorStyles.Bottom;
            cancel.Click += new EventHandler((_sender, _e) => { addForm.Close(); });

            save.Location = new Point(
                addForm.ClientRectangle.Right - save.Width - cancel.Width - 30,
                addForm.ClientRectangle.Bottom - save.Height - 15
            );
            save.Anchor = AnchorStyles.Right | AnchorStyles.Bottom;
            save.Click += new EventHandler((_sender, _e) => {
                if(title.Text != "" && subject.SelectedIndex != -1) {
                    Backend.AddTopic(new() {
                        title = title.Text,
                        draft = draft.Text,
                        final = final.Text,
                        subject = subject.SelectedItem.ToString(),
                    });
                    // ew. but last code sprint and i want to sleep
                    tabControl.Controls.Clear();
                    MainForm_Load(_sender, _e);
                    addForm.Close();
                } else {
                    MessageBox.Show("Hiányzó adatok.", "Hiba");
                }
            });

            addForm.Controls.Add(titleLabel);
            addForm.Controls.Add(title);
            addForm.Controls.Add(draftLabel);
            addForm.Controls.Add(draft);
            addForm.Controls.Add(finalLabel);
            addForm.Controls.Add(final);
            addForm.Controls.Add(subjectLabel);
            addForm.Controls.Add(subject);
            addForm.Controls.Add(cancel);
            addForm.Controls.Add(save);

            // this disables the parent form to prevent editing multiple things at the same time, avoiding
            // race conditions. however, our great friend microsoft has made a horrible decision here too,
            // and hardwired a really annoying beep to clicking an inactive form that cannot be turned off
            // also beeps if i use Show() and set Disabled = true to the parent form
            addForm.ShowDialog();
        }

        private void OpenEditWindow(Topic topic) {
            Form editForm = new() {
                Text = "Tétel szerkesztése",
                StartPosition = FormStartPosition.CenterScreen,
                Size = Size,
                FormBorderStyle = FormBorderStyle.FixedSingle,
                MinimizeBox = false,
                MaximizeBox = false,
            };

            Label titleLabel = new() {
                Text = "Cím",
                Location = new Point(15, 15),
            };
            TextBox title = new() {
                Text = topic.title,
                Width = 150,
                Location = new Point(15, titleLabel.Bottom),
            };

            Label draftLabel = new() {
                Text = "Vázlat",
                Location = new Point(15, title.Bottom + 15),
            };
            TextBox draft = new() {
                Text = topic.draft,
                Width = editForm.ClientSize.Width / 2 - 30,
                Location = new Point(15, draftLabel.Bottom),
                Multiline = true,
            };

            Label finalLabel = new() {
                Text = "Kidolgozás",
                Location = new Point(draft.Right + 15, title.Bottom + 15),
            };
            TextBox final = new() {
                Text = topic.final,
                Width = editForm.ClientSize.Width / 2 - 30,
                Location = new Point(draft.Right + 15, finalLabel.Bottom),
                Multiline = true,
                Enabled = draft.Text != "",
            };

            Label subjectLabel = new() {
                Text = "Tantárgy",
                Location = new Point(draft.Right + 15, 15),
            };
            ComboBox subject = new() {
                Width = 150,
                Location = new Point(draft.Right + 15, titleLabel.Bottom),
                DropDownStyle = ComboBoxStyle.DropDownList,
            };
            foreach(Subject availableSubject in Backend.QuerySubjects()) {
                subject.Items.Add(availableSubject);
                if(availableSubject == topic.subject) {
                    subject.SelectedIndex = subject.Items.Count - 1;
                }
            }

            Button cancel = new() {
                Text = "Elvetés",
            };

            Button save = new() {
                Text = "Frissítés",
            };

            draft.Height = editForm.ClientSize.Height - draft.Bottom - 15 - save.Height;
            // hotfix to not allow final text with an empty draft (as easily, still doable but this is fine
            // for a rudimentary hotfix in alpha or a demo)
            draft.TextChanged += new EventHandler((_sender, _e) => { final.Enabled = draft.Text != ""; });
            final.Height = editForm.ClientSize.Height - final.Bottom - 15 - save.Height;

            cancel.Location = new Point(
                editForm.ClientRectangle.Right - cancel.Width - 15,
                editForm.ClientRectangle.Bottom - cancel.Height - 15
            );
            cancel.Anchor = AnchorStyles.Right | AnchorStyles.Bottom;
            cancel.Click += new EventHandler((_sender, _e) => { editForm.Close(); });

            save.Location = new Point(
                editForm.ClientRectangle.Right - save.Width - cancel.Width - 30,
                editForm.ClientRectangle.Bottom - save.Height - 15
            );
            save.Anchor = AnchorStyles.Right | AnchorStyles.Bottom;
            save.Click += new EventHandler((_sender, _e) => {
                Backend.EditTopic(new() {
                    id = topic.id,
                    title = title.Text,
                    draft = draft.Text,
                    final = final.Text,
                    subject = subject.SelectedItem.ToString(),
                });
                // ew. but last code sprint and i want to sleep
                tabControl.Controls.Clear();
                MainForm_Load(_sender, _e);
                editForm.Close();
            });

            editForm.Controls.Add(titleLabel);
            editForm.Controls.Add(title);
            editForm.Controls.Add(draftLabel);
            editForm.Controls.Add(draft);
            editForm.Controls.Add(finalLabel);
            editForm.Controls.Add(final);
            editForm.Controls.Add(subjectLabel);
            editForm.Controls.Add(subject);
            editForm.Controls.Add(cancel);
            editForm.Controls.Add(save);

            // this disables the parent form to prevent editing multiple things at the same time, avoiding
            // race conditions. however, our great friend microsoft has made a horrible decision here too,
            // and hardwired a really annoying beep to clicking an inactive form that cannot be turned off
            // also beeps if i use Show() and set Disabled = true to the parent form
            editForm.ShowDialog();
        }

        private void OpenTopicViewWindow(Topic topic) {
            // the MessageBox class doesn't provide any way to change the text on its buttons, so i will just use OK
            // the only way to really do this would be to create my own class, which i am not willing to do right now
            MessageBox.Show(
                $"Vázlat:\n{topic.draft}\n\nKidolgozás:\n{topic.final}\n\nUtoljára módosítva:\n{topic.lastModified:yyyy-MM-dd}",
                topic.title
            );
        }

        private void OpenSearchWindow(string query) {
            Form searchForm = new() {
                Text = "Keresés eredménye",
                Size = Size,
            };
            GenerateTopicCards(searchForm, Backend.SearchTopics(query), 0);

            searchForm.ShowDialog();
        }
    }
}
