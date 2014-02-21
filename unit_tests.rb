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