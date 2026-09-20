<?php
      // \site\plugins\heineref_securitytxt\index.php
/**
 * security.txt
 *
 * shows 'security.txt' at your 'https://example.com/.well-known/security.txt'
 *
 * like     https://www.rfc-editor.org/info/rfc9116/ - published: April 2022
 * see also https://heise.de/-11402351 Missing contact option: Germany is sleeping on security via security.txt
 *
 * it does not implements a "PGP Signature" yet. The German BSI does not signs it "security.txt" yet (as of 2026-09-04).
 *
 * @author        HeinerEF
 * @last updated  2026-09-19 by HeinerEF
 * @updated       2026-09-11 by HeinerEF
 * @updated       2026-09-06 by HeinerEF
 * @updated       2026-08-30 by HeinerEF
 * @updated       2026-08-22 by HeinerEF
 * @created       2026-08-08 by HeinerEF
 */


use Kirby\Toolkit\Date;

Kirby::plugin('heineref/securitytxt', [
  'blueprints' => [ // look also at: "/site/blueprints/tabs", which overwrite the following file(s)
    'tabs/securitytxt' => __DIR__ . '/blueprints/tabs/securitytxt.yml',
  ],
  'routes' => [
    [
      'pattern' => [
                    'security.txt',
                    '.well-known/security.txt'
                   ],
      'action'  => function() {
        if (site()->securityshowsecuritytxt()->isTrue()):
          if (site()->securityoptions() == 'date'):
            $expiresDate = site()->securityexpires()->toDate('Y-m-d\TH:i:s\Z');
          else:
            $expiresDate = new Date('last day of last month 12:00'); // the expiration base date changes only once a month
            $interval = DateInterval::createFromDateString(site()->securityvalidity());
            $expiresDate->add($interval);
            $expiresDate = str_replace(' ', 'T', substr($expiresDate->toString('datetime'), 0, 19)) . 'Z'; // 'Z' (= 'GMT'), not 'z' according to the RFC errata dated 2022-12-10 to Section 2.5.5 !
          endif;
          $canonicalUrl = kirby()->url() . '/.well-known/security.txt'; // this URL does NOT include the language code (such as "/en/")!
          $securitytxt = Str::ascii( str_replace('%url%', kirby()->url(), site()->securitytext()) );
          $securitytxt = $securitytxt
          ."\n\n".'# Canonical link'."\n".
          'Canonical: ' . $canonicalUrl
          ."\n\n".'# Expires'."\n".
          'Expires: ' . $expiresDate."\n";
          return new Response($securitytxt, 'text/plain');
        else:
          return false; // = "security.txt" not found
        endif;
      }
    ],
  ],
]);
