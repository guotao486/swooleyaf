--
-- Created by IntelliJ IDEA.
-- User: jw
-- Date: 2018/3/12 0012
-- Time: 18:07
-- To change this template use File | Settings | File Templates.
--

local module = {}

function module.isOpen(confVal)
    local tag = confVal == 'on' and true or false
    return tag
end

function module.readRule(file)
    local rules = {}
    local fd = io.open(file, "r")
    if fd ~= nil then
        for rule in fd:lines() do
            table.insert(rules, rule)
        end

        fd:close()
    end

    return rules
end

function module.getReqBoundary()
    local needHeaders = ngx.req.get_headers()["content-type"]
    if not needHeaders then
        return nil
    end

    local needHeader = ''
    if type(needHeaders) == 'table' then
        needHeader = needHeaders[1]
    else
        needHeader = needHeaders
    end

    local reqBoundary = string.match(needHeader, ";%s*boundary=\"([^\"]+)\"")
    if reqBoundary then
        return reqBoundary
    else
        return string.match(needHeader, ";%s*boundary=([^\",;]+)")
    end
end

return module