// i really don't want to place it here, but having a separate file would be even worse
// because it's just one single type alias. and it's global, so it has to be the first
// thing in the file anyway. and a file containing a single line is unnecessary.
global using Subject = string;

using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace exam_prep_desktop {
    internal struct Topic {
        public int id;
        public string title;
        public string draft;
        public string final;
        public DateTime lastModified;
        public Subject subject;
    }
}
