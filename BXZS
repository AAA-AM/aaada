local Plots = workspace:WaitForChild("Plots")
local COLOR = Color3.fromRGB(55, 185, 255)
local FLOOR_TOL, DEDUP_R = 8, 10
local old = workspace:FindFirstChild("__PodiumMarkers")
if old then old:Destroy() end
local holder = Instance.new("Folder")
holder.Name = "__PodiumMarkers"
holder.Parent = workspace
local function baseBounds(slot)
	local target = slot:FindFirstChild("Base") or slot
	if target:IsA("Model") then
		local ok, cf, sz = pcall(function() return target:GetBoundingBox() end)
		if ok then return cf, sz end
	elseif target:IsA("BasePart") then
		return target.CFrame, target.Size
	end
end
local function makeBox(cf, size)
	local a = Instance.new("Part")
	a.Anchored = true
	a.CanCollide = false
	a.CanQuery = false
	a.CanTouch = false
	a.Transparency = 1
	a.Size = size
	a.CFrame = cf
	a.Parent = holder
	local b = Instance.new("SelectionBox")
	b.Adornee = a
	b.Color3 = COLOR
	b.SurfaceColor3 = COLOR
	b.LineThickness = 0.06
	b.Transparency = 0
	b.SurfaceTransparency = 0.82
	b.Parent = a
end
local function floorOffsets()
	local best
	for _, plot in ipairs(Plots:GetChildren()) do
		local pods = plot:FindFirstChild("AnimalPodiums")
		if pods then
			local ys = {}
			for _, sl in ipairs(pods:GetChildren()) do
				local cf = baseBounds(sl)
				if cf then ys[#ys + 1] = cf.Position.Y end
			end
			table.sort(ys)
			local lv = {}
			for _, y in ipairs(ys) do
				local f = false
				for _, l in ipairs(lv) do if math.abs(l - y) <= FLOOR_TOL then f = true break end end
				if not f then lv[#lv + 1] = y end
			end
			if not best or #lv > #best then best = lv end
		end
	end
	local offs = {}
	if best and #best >= 2 then for i = 2, #best do offs[#offs + 1] = best[i] - best[1] end end
	return offs
end
local function build()
	for _, c in ipairs(holder:GetChildren()) do c:Destroy() end
	local offs = floorOffsets()
	for _, plot in ipairs(Plots:GetChildren()) do
		local pods = plot:FindFirstChild("AnimalPodiums")
		if pods then
			local slots, live, minY = {}, {}, math.huge
			for _, sl in ipairs(pods:GetChildren()) do
				local cf, sz = baseBounds(sl)
				if cf then
					slots[#slots + 1] = { cf = cf, sz = sz }
					live[#live + 1] = cf.Position
					minY = math.min(minY, cf.Position.Y)
				end
			end
			for _, s in ipairs(slots) do makeBox(s.cf, s.sz) end
			for _, s in ipairs(slots) do
				if s.cf.Position.Y <= minY + FLOOR_TOL then
					for _, dy in ipairs(offs) do
						local up = s.cf + Vector3.new(0, dy, 0)
						local exists = false
						for _, lp in ipairs(live) do if (lp - up.Position).Magnitude <= DEDUP_R then exists = true break end end
						if not exists then makeBox(up, s.sz) end
					end
				end
			end
		end
	end
end
build()
local pending = false
local function rebuild()
	if pending then return end
	pending = true
	task.delay(0.4, function() pending = false build() end)
end
local function watch(plot)
	local pods = plot:WaitForChild("AnimalPodiums", 20)
	if pods then
		pods.ChildAdded:Connect(rebuild)
		pods.ChildRemoved:Connect(rebuild)
	end
end
for _, plot in ipairs(Plots:GetChildren()) do task.spawn(watch, plot) end
Plots.ChildAdded:Connect(function(plot)
	task.spawn(watch, plot)
	rebuild()
end)
