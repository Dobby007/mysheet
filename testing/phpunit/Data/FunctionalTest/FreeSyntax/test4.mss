/* taken from: https://developer.mozilla.org/en-US/docs/Web/CSS/@document */
@document url(http://www.w3.org/),
               url-prefix(http://www.w3.org/Style/),
               domain(mozilla.org),
               regexp("https:.*")
{
  /* CSS rules here apply to:
     + The page "http://www.w3.org/".
     + Any page whose URL begins with "http://www.w3.org/Style/"
     + Any page whose URL's host is "mozilla.org" or ends with
       ".mozilla.org"
     + Any page whose URL starts with "https:" */

  /* make the above-mentioned pages really ugly */
  body { color: purple; background: yellow; }
}