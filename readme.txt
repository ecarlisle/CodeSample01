Write a program which stores a list of IP addresses. It should accept HTTP requests on port 9999 at three endpoints:

POST /ip/add
The body of the POST will be a JSON encoded list of IP addresses (see below). Do not worry about proper error handling, you can assume the request is correctly formatted.

Your program should add each IP address to its list of IPs if that IP isn't already in the list. Otherwise, just ignore it and move on.

GET /ip/get/<ip_address>
A request to /ip/get/1.2.3.4 should return "1.2.3.4" if that address is in the list of IP addresses, and return an HTTP 500 status code response otherwise.

GET /ip/all
A request to /ip/all should return a JSON-encoded list of all the IP addresses currently in the program's list of IP addresses.

DELETE /ip/all
A DELETE request to /ip/all should remove all IP addresses from the server's list

Example JSON IP list

[
  "1.2.3.4",
  "4.5.6.7",
  "20.253.90.9"
]
——————————

Unit Tests

require "test/unit"
require "net/http"
require "json"

class TestIpAddressServer < Test::Unit::TestCase
  def setup
    @http = Net::HTTP.start("localhost", 9999)
    @http.delete("/ip/all")
  end

  def test_delete_ip
    r = @http.post("/ip/add", '["1.2.3.4","2.3.4.5"]', {"content-type" => "application/json"})
    assert_equal(r.code, "200")

    r = @http.delete("/ip/all")
    assert_equal("200", r.code)

    r = @http.get("/ip/all")
    assert_equal("200", r.code)
    j = JSON.load(r.body)
    assert_equal([], j)
  end

  def test_get_ip
    r = @http.get("/ip/get/1.2.3.4")
    assert_equal("500", r.code)

    r = @http.post("/ip/add", '["1.2.3.4","2.3.4.5"]', {"content-type" => "application/json"})
    assert_equal(r.code, "200")

    r = @http.get("/ip/get/1.2.3.4")
    assert_equal(r.code, "200")
    assert_equal(r.body, "1.2.3.4")
  end

  def test_add_ip
    r = @http.post("/ip/add", '["1.2.3.4","2.3.4.5"]', {"content-type" => "application/json"})
    assert_equal(r.code, "200")

    r = @http.get("/ip/all")
    assert_equal("200", r.code)
    j = JSON.load(r.body)
    assert_equal(["1.2.3.4", "2.3.4.5"], j)
  end
end
