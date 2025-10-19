# Hetzner FRITZ!Box DynDNS updater

An Hetzner DNS updater triggered by a FRITZ!Box.  
Small PHP project that updates Hetzner DNS records (A/AAAA) when your FRITZ!Box obtains a new public IP — useful to keep a hostname pointed to your home network.

IMPORTANT: This script works ONLY with Hetzner's new DNS Console / [new DNS API](https://docs.hetzner.cloud/reference/cloud#dns). The updater uses zone and rrset names (zone name, rrset name, rrset type) rather than numeric zone/record IDs.

## Features
- Lightweight PHP script(s) to update Hetzner DNS RRSets
- Intended to be called by a FRITZ!Box on reconnection
- Uses the caller IP (or an explicit ip parameter) to update A/AAAA records
- Refer to DNS entries by names (zone, rrset name, rrset type) — no numeric IDs required
- Protectable with a simple API token (local secret)

## Requirements
- PHP 7.4+ (or compatible PHP runtime)
- A web-accessible server (container, VM, physical server) with HTTPS recommended
- Hetzner DNS API token for the new DNS Console/API
- FRITZ!Box that can call a URL on reconnect (update/notification/dynamic DNS URL)

## Getting a Hetzner API token
Create an API token in the Hetzner Console (Console / Security / API tokens) with DNS access (read & write). See Hetzner DNS docs:
[https://docs.hetzner.cloud/reference/cloud#dns](https://docs.hetzner.cloud/reference/cloud#dns)

Keep the token secret — you'll need it for the updater.

## Quick overview — how it works
1. Deploy the PHP script(s) to a web server reachable by your FRITZ!Box.  
1. Configure FRITZ!Box to call the script's URL when the router connects/reconnects (Internet / Permit Access / DynDNS / http://your-server/path-to-script/dns_update_v2_ipv4.php?ipv4address=<ipaddr>).
1. The script authenticates the request (via a local secret token), gets the IP you passed on GET, and calls Hetzner's new DNS API to update the rrset using zone name + rrset name + rrset type.

## Installation
1. Clone or copy the repository files to your PHP-capable web server directory.  
1. Configure environment/config values (see Configuration).  
1. Ensure the webserver user can read the config files.

## Configuration
This updater targets Hetzner's new DNS API and refers to DNS objects by name. Example variables:

- $apitoken: your Hetzner DNS API token (new DNS Console)
- $zones: an array wich contains a domain list and, for each, a tuple of RRSet Name/Type to update
- $timezone: the timezone where your server is located (useful only for date/time on comment)

## Example usage / URL for FRITZ!Box
Make the script reachable at a URL such as:

http://your-server/path-to-script/dns_update_v2_ipv4.php?ipv4address=<ipaddr>

When FRITZ!Box calls that URL, the script should:
- validate the IP given in input
- call Hetzner's new DNS API to update the rrsets identified by $zones array (ZONE_NAME, RRSET_NAME and RRSET_TYPE)

Manual test with curl:
curl -s "http://your-server/path-to-script/dns_update_v2_ipv4.php?ipv4address=<ipaddr>"

Provide an explicit IP:
curl -s "http://your-server/path-to-script/dns_update_v2_ipv4.php?ipv4address=1.2.3.4"

The script should return a JSON response indicating success or failure and the IP it set.

## Security & hardening
- Protect the endpoint keeping the server on your own network.

## Compatibility note
This project targets Hetzner's new DNS Console/API and will not work with legacy DNS API endpoints or older console versions. If Hetzner changes API semantics again, the script may require updates.

## Contributing
Contributions and improvements welcome. Please open a PR or issue with a clear description and reproduction steps.

---

If you want, I can also draft a ready-to-use update.php and a sample .env tuned to the new Hetzner DNS API so you can drop them into your repo.
