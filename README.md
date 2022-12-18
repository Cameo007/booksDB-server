# booksDB - Managing read and unread books
Repository for the booksDB server files. The android app's source code is available [here](https://github.com/Cameo007/booksDB-app/).

## Requirements
- A MySQL database named "booksDB"
- [GuzzleHttp](https://github.com/guzzle/guzzle) installed via composer

## Setup
1. Insert the username & password of the MySQL user in the `/api/bdb.php`
2. Set `$baseURL` to you your server's base URL.
3. Notice that I assume that your website is located at `/var/www/html/`. If not, you need to change `/var/www/html/lang.json` in `lang.php` to the path, where `lang.json` is stored on your server.
4. It is not necessary but you may want to adjust the meta-tags in `bdb.php` and the text in the footer. But if you change the footer's text don't forget to adjust it in the `lang.json` file too.

## API
[![swagger-api validator-badge](https://validator.swagger.io/validator?url=https://mint.jojojux.de/swagger/src/bdb.json)](https://mint.jojojux.de/swagger/src/bdb.json)
My API is located at `/api/bdb.php` and [documentated with Swagger](https://mint.jojojux.de/swagger).
## Libraries
- [Bootstrap](https://getbootstrap.com/)
- [jQuery](https://jquery.com/)
- [langmng](http://langmng.glitch.me/langmng.js)
- [GuzzleHttp](https://github.com/guzzle/guzzle)
  
## License
```
booksDB - Managing read and unread books
Copyright (C) 2022  Pascal Dietrich

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This app is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301
USA
```
