{embed="site/_header"}
 <main>
        <?php 
        
        include 'http://localhost/ee/data-functions.php';

        ?>

        <section class="jumbotron jumbotron-fluid text-center">
            <h1 class="text-center font-weight-light">Simple Expression Engine Blog</h1>
        </section>
        <div class="container mt-1 mb-5">
            <div class="row">
                <div class="col-8">    
                    <br>
                    <h2>Latest Posts</h2>
                    <hr>
                    {exp:channel:entries channel="blog" orderby="date" sort="desc" limit="3"}
                        <div class="card mt-3">
                            <div class="card-header">
                                <a href="blog/{url_title}"><h3>{title}</h3></a>
                                Posted: {entry_date format="%M %d, %Y"}
                                By: {author}
                            </div>
                            <div class="card-body">
                                {exp:excerpt words="100"}
                                    {post}
                                {/exp:excerpt}
                                <br>
                            <a href="blog/{url_title}" class="btn btn-primary mt-3">Read More</a>
                            </div>
                        </div>
                    {/exp:channel:entries}
                </div>
                <div class="col-4">
                    <br>
                    <h3>Categories</h3>
                    <hr>
                    {exp:channel:categories channel="blog"}
                    <a href="{path='blog/category'}">{category_name}</a>
                    {/exp:channel:categories}
                    <br>
                    
                    <div class="card">
                        <div class="card-body">
                            <form target="_self" method="POST">
                                <div class="form-group">
                                    <label for="first-name-input">Name:</label>
                                    <input type="text" name="user-firstname" class="form-control" placeholder="Name" id="first-name-input">
                                </div>
                                <div class="form-group">
                                    <label for="last-name-input">Surname:</label>
                                    <input type="text" name="user-surname" class="form-control" placeholder="Surname" id="last-name-input">
                                </div>
                                <div class="form-group">
                                    <label for="emailAddressInput">Email Address:</label>
                                    <input type="email" name="user-email" class="form-control" placeholder="Email Address" id="email-address-input">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
              </div>
        </div>
    </main>
    {embed="site/_footer"}