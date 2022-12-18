# booksDB - Managing read and unread books
Repository for the booksDB server files. The android app's source code is available [here](https://github.com/Cameo007/booksDB-app/).

## Requirements
- A MySQL database named "booksDB"
- [GuzzleHttp](https://github.com/guzzle/guzzle) installed via composer

## Setup
1. Insert the username & password of the MySQL user in the `/api/bdb.php`
2. Notice that I assume that your website is located at `/var/www/html/`. If not, you need to change `/var/www/html/lang.json` in `lang.php` to the path, where `lang.json` is stored on your server.

## Libraries
  
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
