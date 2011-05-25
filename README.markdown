#Simple CRUD

Simple CRUD is a very simple CRUD implementation written in PHP. It basically maps a database entry (At the moment it uses mysql but, everything is handled by a pdo object thus, could use almost any if you've got the appropriate plugin) to a php object. 


    //Create
    $entry = new entry();
    $entry->title = "Hello World";
    $entry->body = "Lorem ipsum dolor sit amet?";
    $entry->date_published = date('Y-m-d');
    $entry->save();
    
    //Read
    $entry = entry::find(3);
    echo($entry->title); //Hello World
    echo($entry->body); //Lorem Ipsum dolor sit amet?;
    
    //Update
    $entry = entry::find(3);
    $entry->title = "Bye Bye";
    $entry->body = "lol?";
    $entry->save();
    
    //Delete
    $entry = entry::find(7);
    $entry->delete();

I will most probably drop the code now since, I don't really have the desire or the need to continue developing. It was a short 5 hours idea. I hope someone might find a use for it. Although it's so basic it's probably useless since, you can only find and work with IDs. 

In the future I might make it so you can find by any column names.

BTW, since I built this for myself and all of my ids are named id, it assumes that your primary key is named id. 